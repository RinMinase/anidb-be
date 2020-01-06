<?php

/**
 * @api {get} /api/changelog/:limit? Front-end Changelog
 * @apiName ChangelogFE
 * @apiGroup Release
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiParam {String} limit Page Limit (Optional)
 *
 * @apiSuccess {Array} dep Dependencies type of changes
 * @apiSuccess {Array} fix Fix type of changes
 * @apiSuccess {Array} new New type of changes
 * @apiSuccess {Array} improve Improvement type of changes
 * @apiSuccess {String} title Date of changelist
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     [
 *       "changes_{{date}}": {
 *         dep: [
 *           {
 *             date: "Jan 01, 2020 00:00"
 *             email: "sample@mail.com"
 *             name: "Owner"
 *             message: "updated sub-dependencies"
 *             module: ""
 *             url: "{{ GitHub commit URL }}"
 *           }
 *         ],
 *         fix: [],
 *         new: [],
 *         improve: [],
 *         title: "Jan 01, 2020",
 *       },
 *       "changes_{{date}}": { ... },
 *     ]
 *
 * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": "Unauthorized",
 *       "message": "Unauthorized"
 *     }
 */

/**
 * @api {get} /api/changelog-be/:limit? Back-end Changelog
 * @apiName ChangelogBE
 * @apiGroup Release
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiParam {String} limit Page Limit (Optional)
 *
 * @apiSuccess {Array} dep Dependencies type of changes
 * @apiSuccess {Array} fix Fix type of changes
 * @apiSuccess {Array} new New type of changes
 * @apiSuccess {Array} improve Improvement type of changes
 * @apiSuccess {String} title Date of changelist
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     [
 *       "changes_{{date}}": {
 *         dep: [
 *           {
 *             date: "Jan 01, 2020 00:00"
 *             email: "sample@mail.com"
 *             name: "Owner"
 *             message: "updated sub-dependencies"
 *             module: ""
 *             url: "{{ GitHub commit URL }}"
 *           }
 *         ],
 *         fix: [],
 *         new: [],
 *         improve: [],
 *         title: "Jan 01, 2020",
 *       },
 *       "changes_{{date}}": { ... },
 *     ]
 *
 * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": "Unauthorized",
 *       "message": "Unauthorized"
 *     }
 */

/**
 * @api {get} /api/issues/:limit? Issues List
 * @apiName Issues
 * @apiGroup Release
 *
 * @apiHeader {String} api-key Back-end API Key
 * @apiParam {String} limit Page Limit (Optional)
 *
 * @apiSuccess {String} date Date of posted issue
 * @apiSuccess {Array} labels Issue labels
 * @apiSuccess {Number} number Issue number
 * @apiSuccess {String} title Issue title
 * @apiSuccess {String} url GitHub issue URL
 *
 * @apiSuccessExample Success Response
 *     HTTP/1.1 200 OK
 *     [
 *       {
 *         date: "Jan 01, 2020",
 *         labels: [
 *           {
 *             class: "type-enhancement"
 *             name: "ENHANCEMENT"
 *           }, {
 *             class: "type-priority-high"
 *             name: "HIGH"
 *           },
 *         ],
 *         number: 100,
 *         title: "This is a sample issue title",
 *         url: {{ GitHub issue URL }},
 *       }, { ... },
 *     }
 *
 * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
 *
 * @apiErrorExample Unauthorized
 *     HTTP/1.1 401 Forbidden
 *     {
 *       "status": "Unauthorized",
 *       "message": "Unauthorized"
 *     }
 */
