<?php

/**
 * @api {post} /api/login User Login
 * @apiName UserLogin
 * @apiGroup User
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiBody {String} username Username
 * @apiBody {String} password Password
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
 * @apiError Invalid Either the Username or Password is not provided
 * @apiError InvalidCredentials Either the Username or Password of the User is invalid
 * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
 *
 * @apiErrorExample Invalid
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": 400,
 *       "message": "username and password fields are required"
 *     }
 *
 * @apiErrorExample InvalidCredentials
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": 400,
 *       "message": "username or password is invalid"
 *     }
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": 401,
 *       "message": "Unauthorized"
 *     }
 */

/**
 * @api {post} /api/logout User Logout
 * @apiName UserLogout
 * @apiGroup User
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiBody {String} token Token received from logging-in
 *
 * @apiSuccess {String} status Status code
 * @apiSuccess {String} message Status message
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     {
 *       "status": 200,
 *       "message": "Success"
 *     }
 *
 * @apiError Invalid There is no token provided
 * @apiError InvalidToken The token provided is invalid
 * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
 *
 * @apiErrorExample Invalid
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": 400,
 *       "message": "token is required"
 *     }
 *
 * @apiErrorExample InvalidToken
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "status": 400,
 *       "message": "Session Token not found"
 *     }
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": 401,
 *       "message": "Unauthorized"
 *     }
 */

/**
 * @api {post} /api/register User Registration
 * @apiName UserRegistration
 * @apiGroup User
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiBody {String} username Username
 * @apiBody {String} password Password
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
