<?php

/**
 * @api {post} /api/mal/:id Retrieve Title Information
 * @apiName RetrieveTitleInfo
 * @apiGroup MAL
 *
 * @apiHeader {String} token User login token
 * @apiParam {Number} id MAL Title ID
 *
 * @apiSuccess {String} url MAL Title URL
 * @apiSuccess {String} title Full title
 * @apiSuccess {String} synonyms Variants
 * @apiSuccess {Number} episodes Number of episodes
 * @apiSuccess {String} premiered Premiered Season and Year
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     {
 *       "url": "https://myanimelist.net/anime/37430/Tensei_shitara_Slime_Datta_Ken",
 *       "title": "Tensei shitara Slime Datta Ken",
 *       "synonyms": "TenSura",
 *       "episodes": 24,
 *       "premiered": "Fall 2018",
 *     }
 *
 * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": "Unauthorized",
 *       "message": "Unauthorized"
 *     }
 */

/**
 * @api {post} /api/mal/:query Query Titles
 * @apiName QueryTitles
 * @apiGroup MAL
 *
 * @apiHeader {String} token User login token
 * @apiParam {String} query Query string to match
 *
 * @apiSuccess {Object[]} data MAL Title ID
 * @apiSuccess {String} data.id MAL title id
 * @apiSuccess {String} data.title Full title
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     [
 *       {
 *         "id": "37430",
 *         "title": "Tensei shitara Slime Datta Ken",
 *       },
 *       {
 *         "id": "8475",
 *         "title": "Asura",
 *       }, { ... }
 *     ]
 *
 * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": "Unauthorized",
 *       "message": "Unauthorized"
 *     }
 */
