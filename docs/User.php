<?php

/**
 * @api {post} /api/login User Login
 * @apiName login
 * @apiGroup User
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiParam {String} username Username
 * @apiParam {String} password Password
 *
 * @apiSuccess {String} username Username of the Logged-in user
 * @apiSuccess {String} role Role of the Logged-in user
 * @apiSuccess {String} token Token to be used for API calls
 * @apiSuccess {String} timeout Unix timestamp for session expiry date and time
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     {
 *       "username": "username",
 *       "role": "3"
 *       "token": "{{ token }}"
 *       "timeout": "{{ timestamp }}"
 *     }
 *
 * @apiError Invalid Either the Username or Password of the User is invalid
 * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
 *
 * @apiErrorExample Invalid
 *     HTTP/1.1 404 Not Found
 *     "username" or "password" is invalid
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 404 Not Found
 *     "username" or "password" is invalids
 */
