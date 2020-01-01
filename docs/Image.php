<?php

/**
 * @api {get} /api/img/:param Retrieve Image URL
 * @apiName retrieve
 * @apiGroup Image
 *
 * @apiHeader {String} token User login token
 * @apiParam {Path} param Complete image file path (i.e. 'assets/test.jpg')
 *
 * @apiSuccess {String} url Signed URL of image
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     https://storage.googleapis.com/example.appspot.com/assets/test.jpg?X-Goog-Algorithm=GOOG4-RSA-SHA256&X-Goog-Credential=example%40appspot.gserviceaccount.com{{url and key contents}}
 *
 * @apiError NoSuchKey The specified key does not exist
 *
 * @apiErrorExample Error Response
 *     HTTP/1.1 200 Success Response (Error is shown after using the URL provided)
 *     https://storage.googleapis.com/example.appspot.com/assets/test.jpg?X-Goog-Algorithm=GOOG4-RSA-SHA256&X-Goog-Credential=example%40appspot.gserviceaccount.com{{url and key contents}}
 */
