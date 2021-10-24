<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Database\Eloquent\Collection;
use \Illuminate\Http\Response as Res;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * @var int
     */
    protected $statusCode = Res::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @internal param $message
     * @return Controller
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $data
     * @param int $code
     * @param array $headers
     * @return mixed
     */
    public function respond($data, $code = Res::HTTP_OK, $headers = ['message' => 'Request successful.'])
    {
        return response()->json($data, $code, $headers);
    }

    /**
     * @param null $data
     * @param string $message
     * @return mixed
     */
    public function responseWithItem($data = null, $message = 'Request successful.')
    {
        return $this->respond([
            'status_code' => Res::HTTP_OK,
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * @param Paginator $paginate
     * @param $data
     * @param string $message
     * @param array $meta
     * @return mixed
     */
    protected function respondWithPagination(Paginator $paginate, $data, $message = 'Paginated Items', array $meta = ['message' => 'Request successful.'])
    {
        $paginator = [
            'total_count'  => $paginate->total(),
            'per_page'     => $paginate->perPage(),
            'current_page' => $paginate->currentPage(),
            'last_page' => $paginate->lastPage(),
            'total_pages'  => ceil($paginate->total() / $paginate->perPage()),
            'from'         => $paginate->firstItem(),
            'to'           => $paginate->lastItem(),
        ];
        return $this->respond([
            'status_code' => Res::HTTP_OK,
            'success'      => true,
            'message'     => $message,
            'paginator'   => $paginator,
            'data'        => $data,
        ], Res::HTTP_OK, $meta);
    }

    /**
     * @param string $message
     * @param array $meta
     * @return mixed
     */
    public function respondNotFound($message = 'Item not found!', array $meta = ['message' => 'Item not found.'])
    {
        return $this->respond([
            'status_code' => Res::HTTP_NOT_FOUND,
            'success' => false,
            'message' => $message,
            'data' => (object) array(),
        ], Res::HTTP_NOT_FOUND, $meta);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function respondInternalError($message = 'An error occurred on the server.')
    {
        $code = Res::HTTP_INTERNAL_SERVER_ERROR;
        $meta = ['message' => 'An error occurred on the server.'];
        return $this->respond([
            'status_code' => $code,
            'success' => false,
            'errors' => (object) array(),
            'message' => $message,
            'data' => (object) array(),
        ], $code, $meta);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondValidationError($errors, $message = '')
    {
        return $this->respond([
            'status_code' => Res::HTTP_UNPROCESSABLE_ENTITY,
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'data' => (object) array()
        ], Res::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param Collection $collection
     * @param string $message
     * @param array $meta
     * @return Res
     */
    protected function collectionResponse(Collection $collection, $message = 'Request successful.', array $meta = ['message' => 'Request successful.'])
    {
        return $this->respond([
            'status_code' => Res::HTTP_OK,
            'success' => true,
            'message' => $message,
            'data' => $collection,
        ], Res::HTTP_OK, $meta);
    }


    /**
     * @param $message
     * @param int $errorCode
     * @param array $meta
     * @return mixed
     */
    public function respondWithError($message, $errorCode = Res::HTTP_UNAUTHORIZED,  array $meta = ['message' => 'Request failed.'])
    {
        return $this->respond([
            'status_code' => $errorCode,
            'success' => false,
            'message' => $message,
            'data' => (object) array(),
        ], Res::HTTP_UNAUTHORIZED, $meta);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondWithMessage($message)
    {
        return $this->respond([
            'status_code' => Res::HTTP_OK,
            'success' => true,
            'message' => $message,
            'data' => (object) array(),
        ], Res::HTTP_OK);
    }


    /**
     * @param       $data
     * @param string $message
     * @param array $meta
     * @return Res
     */
    public function itemCreatedResponse($data, $message = 'Item Created', $meta = ['message' => 'Request successful.'])
    {
        if (empty($data)) {
            $data = (object) array();
        }
        return $this->respond([
            'status_code' => Res::HTTP_CREATED,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], Res::HTTP_OK, $meta);
    }

    /**
     * @param array $data
     * @param string $message
     * @param array $meta
     * @return Res
     */
    public function itemUpdatedResponse($data = array(), $message = 'Item Updated', $meta = ['message' => 'Request successful.'])
    {

        if (empty($data)) {
            $data = (object) array();
        }

        return $this->respond([
            'status_code' => Res::HTTP_OK,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], Res::HTTP_OK, $meta);
    }

    /**
     * @param       $data
     * @param string $message
     * @param array $meta
     * @return Res
     */
    public function itemDeletedResponse($data, $message = 'Item Deleted', $meta = ['message' => 'Request successful.'])
    {
        if (empty($data)) {
            $data = (object) array();
        }

        return $this->respond([
            'status_code' => Res::HTTP_OK,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], Res::HTTP_OK, $meta);
    }

    /**
     * @param array $data
     * @param string $message
     * @param array $meta
     * @return Res
     * @internal param \League\Fractal\TransformerAbstract|null $transformer
     */
    public function arrayResponse(array $data, $message = 'Request successful.', array $meta = ['message' => 'Request successful.'])
    {
        return $this->respond([
            'status_code' => Res::HTTP_OK,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], Res::HTTP_OK, $meta);
    }

    /**
     * @param object $data
     * @param string $message
     * @param array $meta
     * @return Res
     * @internal param \League\Fractal\TransformerAbstract|null $transformer
     */
    public function objectResponse($data, $message = 'Request successful.', array $meta = ['message' => 'Request successful.'])
    {
        return $this->respond([
            'status_code' => Res::HTTP_OK,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], Res::HTTP_OK, $meta);
    }

    /**
     * @param $attribute
     * @param $value
     * @return false|int
     */
    public function passes($attribute, $value)
    {
        return preg_match("/^[-]?((([0-8]?[0-9])(\.(\d{1,8}))?)|(90(\.0+)?)),\s?[-]?((((1[0-7][0-9])|([0-9]?[0-9]))(\.(\d{1,8}))?)|180(\.0+)?)$/", $value);
    }
}
