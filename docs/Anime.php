<?php

/**
 * @api {get} /api/anime Retrieve Anime
 * @apiName RetrieveAnime
 * @apiGroup Anime
 *
 * @apiHeader {String} token User login token
 * @apiParam {String} limit Page Limit (Optional)
 * @apiParam {String='hdd','release','title'} group Grouping (Optional)
 *
 * @apiSuccess {Object[]} data Anime Data
 * @apiSuccess {Number} dateFinished Date Finished in Unix formatting
 * @apiSuccess {Number} duration Duration in seconds
 * @apiSuccess {Number} filesize Filesize in bytes
 * @apiSuccess {Boolean} inhdd Flag if title is located in HDD
 * @apiSuccess {String='4K 2160p','FHD 1080p','HD 720p','HQ 480p','LQ 360p'} quality Video quality
 * @apiSuccess {Object} rating Rating of Audio, Enjoyment, Graphics and Plot
 * @apiSuccess {String='Winter','Spring','Summer','Fall'} releaseSeason Season in which the title was released
 * @apiSuccess {String} releaseYear Year in which the title was released converted to String
 * @apiSuccess {String} rewatch Comma-separated rewatch dates in Unix formatting
 * @apiSuccess {Number} rewatchLast Last rewatched date in Unix formatting
 * @apiSuccess {String} variants Comma-separated title variants
 * @apiSuccess {Number=0,1,2} watchStatus 0 = Unwatched, 1 = Watched, 2 = Downloaded
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     [
 *       {
 *         "_id": {
 *           "$oid": 1234abcd5678efgh
 *         },
 *         "dateFinished": 1546185600,
 *         "duration": 12345,
 *         "encoder": "encoder",
 *         "episodes": 25,
 *         "filesize": 123456789,
 *         "firstSeasonTitle": "First Season Title",
 *         "inhdd": true,
 *         "offquel": "Offquel1, Offquel2, Offquel3",
 *         "ovas": 1,
 *         "prequel": "Prequel Title",
 *         "quality": "FHD 1080p",
 *         "rating": {
 *             "audio": 5,
 *             "enjoyment": 7,
 *             "graphics": 4,
 *             "plot": 7
 *         },
 *         "releaseSeason": "Spring",
 *         "releaseYear": "2017",
 *         "remarks": "",
 *         "rewatch": "1553270400, 1553260400",
 *         "rewatchLast": 1553270400,
 *         "seasonNumber": 2,
 *         "sequel": "Sequel Title",
 *         "specials": 1,
 *         "title": "Title",
 *         "variants": "Variant1, Variant2",
 *         "watchStatus": 0
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
