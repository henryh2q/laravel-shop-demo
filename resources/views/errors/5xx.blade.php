@include('errors.layout', ['code' => $exception->getStatusCode(), 'title' => __('Something went wrong'), 'message' => __('Please wait a moment and try again.')])
