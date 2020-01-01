<?php

/**
 * @api {post} /api/login User Login
 * @apiName UserLogin
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
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": "Invalid",
 *       "message": "Username or Password is invalid"
 *     }
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": "Unauthorized",
 *       "message": "Unauthorized"
 *     }
 */

/**
 * @api {post} /api/logout User Logout
 * @apiName UserLogout
 * @apiGroup User
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiParam {String} token Token received from logging-in
 *
 * @apiSuccess {String} status Status code
 * @apiSuccess {String} message Status message
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     {
 *       "status": "Success",
 *       "message": "User has been logged-out"
 *     }
 *
 * @apiError Invalid Login session is not found
 * @apiError InvalidToken There is no token provided, or the token provided is invalid
 * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
 *
 * @apiErrorExample Invalid
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": "Invalid",
 *       "message": "Username or Password is invalid"
 *     }
 *
 * @apiErrorExample InvalidToken
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": "InvalidToken",
 *       "message": "Session Token not found or invalid"
 *     }
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": "Unauthorized",
 *       "message": "Unauthorized"
 *     }
 */

/**
 * @api {post} /api/register User Registration
 * @apiName UserRegistration
 * @apiGroup User
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiParam {String} username Username
 * @apiParam {String} password Password
 *
 * @apiSuccess {String} status Status code
 * @apiSuccess {String} message Status message
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     {
 *       "status": "Success",
 *       "message": "User has been registered"
 *     }
 *
 * @apiError Existing Username is already taken
 * @apiError Invalid Either the Username or Password of the User is not provided
 * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
 *
 * @apiErrorExample Existing
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": "Existing",
 *       "message": "Username is already taken"
 *     }
 *
 * @apiErrorExample Invalid
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": "Invalid",
 *       "message": "Username and Password fields are required"
 *     }
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": "Unauthorized",
 *       "message": "Unauthorized"
 *     }
 */
