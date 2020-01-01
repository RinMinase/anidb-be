<?php

/**
 * @api {get} /api/img/:param Retrieve Image URL
 * @apiName ImageRetrieve
 * @apiGroup Image
 *
 * @apiHeader {String} token User login token
 * @apiParam {Path} param Complete image file path (i.e. 'assets/test.jpg')
 *
 * @apiSuccess {String} url Signed URL of image
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     {
 *       "url": "https://storage.googleapis.com/example.appspot.com/assets/test.jpg?X-Goog-Algorithm=GOOG4-RSA-SHA256&X-Goog-Credential=example%40appspot.gserviceaccount.com{{url and key contents}}"
 *     }
 *
 * @apiError Invalid The specified image path does not exist
 *
 * @apiErrorExample Invalid
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": "Invalid",
 *       "message": "Image path is invalid"
 *     }
 */
