<?php

namespace AdsMurai\Infrastructure\UI\Http\SafeBox;

use AdsMurai\Application\Service\SafeBox\AddSafeBoxItemRequest;
use AdsMurai\Application\Service\SafeBox\AddSafeBoxItemService;
use AdsMurai\Application\Service\SafeBox\CreateNewSafeBoxRequest;
use AdsMurai\Application\Service\SafeBox\CreateNewSafeBoxService;
use AdsMurai\Application\Service\SafeBox\OpenSafeBoxRequest;
use AdsMurai\Application\Service\SafeBox\OpenSafeBoxService;
use AdsMurai\Application\Service\SafeBox\RetrieveSafeBoxContentRequest;
use AdsMurai\Application\Service\SafeBox\RetrieveSafeBoxContentService;
use AdsMurai\Infrastructure\Repository\SafeBox\FileCommonPasswordRepository;
use AdsMurai\Infrastructure\Repository\SafeBox\MemorySafeBoxRepository;
use AdsMurai\Infrastructure\Repository\SafeBox\SqliteSafeBoxRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SafeBoxController extends Controller
{
    /**
     * @var FileCommonPasswordRepository
     */
    private $commonPasswordRepository;
    /**
     * @var MemorySafeBoxRepository
     */
    private $safeBoxRepository;

    public function __construct()
    {
        $this->safeBoxRepository =  new SqliteSafeBoxRepository();
        $this->commonPasswordRepository = new FileCommonPasswordRepository();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function getSecurityFromRequest(Request $request)
    {
        $header = $request->header('Authorization');
        $headerArray = explode(' ', $header);

        if (!in_array('Bearer', $headerArray) || !isset($headerArray[1]) || count($headerArray) !== 2) {
            throw new UnauthorizedHttpException('Password not provided');
        }

        return $headerArray[1];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \AdsMurai\Domain\SafeBox\SafeBoxExistsException
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ], $request->all());

        $createNewSafeBoxRequest = new CreateNewSafeBoxRequest($validatedData['name'], $validatedData['password']);
        $createNewSafeBoxService = new CreateNewSafeBoxService(
            $this->safeBoxRepository,
            $this->commonPasswordRepository
        );

        $safeBox = $createNewSafeBoxService->execute($createNewSafeBoxRequest);

        return response()->json(['id' => $safeBox->id()], 200);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \AdsMurai\Domain\SafeBox\WrongPasswordException
     * @throws \Exception
     */
    public function open(string $id, Request $request)
    {
        $password = $this->getSecurityFromRequest($request);
//        $a = $request->query('expirationTime');//todo revisar

        $expirationTime = $request->get('expirationTime') ?? OpenSafeBoxRequest::EXPIRATION;
        $openSafeBoxService = new OpenSafeBoxService($this->safeBoxRepository);
        $openSafeBoxRequest = new OpenSafeBoxRequest($id, $password, $expirationTime);

        $token = $openSafeBoxService->execute($openSafeBoxRequest);

        return response()->json(['token' => $token], 200);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function content(string $id, Request $request)
    {
        $token = $this->getSecurityFromRequest($request);

        $request = new RetrieveSafeBoxContentRequest($id, $token);

        $retrieveSafeBoxContentService = new RetrieveSafeBoxContentService($this->safeBoxRepository);

        $items = $retrieveSafeBoxContentService->execute($request);

        return response()->json(['items' => $items], 200);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItem(string $id, Request $request)
    {
        $token = $this->getSecurityFromRequest($request);

        $validatedData = $request->validate([
            'item' => 'required|string',
        ], $request->all());

        $request = new AddSafeBoxItemRequest($id, $token, $validatedData['item']);

        $addSafeBoxItemService = new AddSafeBoxItemService($this->safeBoxRepository);

        $addSafeBoxItemService->execute($request);

        return response()->json(['Content correctly added to the safebox'], 200);
    }

}