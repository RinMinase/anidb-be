<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>
    {{ config('l5-swagger.documentations.' . $documentation . '.api.title') }}
  </title>
  <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}" />
  <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32" />
  <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}"
    sizes="16x16" />
  <style>
    html {
      box-sizing: border-box;
      overflow: -moz-scrollbars-vertical;
      overflow-y: scroll;
    }

    *,
    *:before,
    *:after {
      box-sizing: inherit;
    }

    body {
      margin: 0;
      background: #fafafa;
    }

    .swagger-ui .info {
      margin-bottom: 0;
    }

    .swagger-ui .scheme-container {
      padding: 0;
      box-shadow: none;
      background: none;

      margin: -85px 0 12px auto;

      position: relative;
      height: 67px;
      max-width: 250px;
    }

    .swagger-ui .scheme-container .schemes {
      position: absolute;
      right: 0;
      width: auto;
    }

    .information-container.wrapper {
      padding-bottom: 12px;
      border-bottom: 1px solid #bbb;
      margin-bottom: 12px;
    }

    /* UNUSED SECTION IN API */
    .swagger-ui .response-col_links {
      display: none;
    }

    /* NORMAL API */

    .swagger-ui .execute-wrapper {
      padding-top: 0;
      padding-bottom: 0;
    }

    .swagger-ui .btn.execute {
      margin-bottom: 20px;
    }

    .swagger-ui table thead tr td, .swagger-ui table thead tr th {
      font-size: 14px;
    }

    .swagger-ui .response-col_status {
      font-size: 16px;
    }

    .swagger-ui table tbody tr td.response-col_status:first-of-type {
      min-width: 4em;
      padding-top: 26px;
      font-size: 16px;
      font-weight: bold;
    }

    .swagger-ui table.parameters .parameters-col_description p {
      margin-top: 0;
      margin-bottom: 12px;
    }

    .swagger-ui .response-col_description .response-col_description__inner p {
      margin-bottom: 0;
    }

    .parameters-container .opblock-description-wrapper p {
      font-size: 18px;
      padding-top: 8px;
      padding-bottom: 3px;
    }

    /* DEPRECATED API */

    .swagger-ui .opblock.opblock-deprecated {
      opacity: 1;
      background: #eee;
      border-color: #bbb;
    }

    .swagger-ui .opblock.opblock-deprecated .opblock-summary-method {
      background: #ccc;
    }

    /* MODAL */

    .swagger-ui .dialog-ux .modal-ux-content {
      text-align: center;
    }

    .swagger-ui .dialog-ux .modal-ux-content .auth-container .wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
    }

    .swagger-ui .dialog-ux .modal-ux-content .auth-container label {
      margin-bottom: 0;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <div id="swagger-ui"></div>

  <script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>
  <script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>
  <script>
    window.onload = function() {
      const ui = SwaggerUIBundle({
        dom_id: '#swagger-ui',
        url: "{!! $urlToDocs !!}",
        operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
        configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
        validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
        oauth2RedirectUrl: "{{ route('l5-swagger.' . $documentation . '.oauth2_callback', [], $useAbsolutePath) }}",

        requestInterceptor: function(request) {
          request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
          return request;
        },

        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],

        plugins: [
          SwaggerUIBundle.plugins.DownloadUrl
        ],

        layout: "StandaloneLayout",
        docExpansion: "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
        deepLinking: true,
        filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
        persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",
        defaultModelsExpandDepth: -1,
      })

      window.ui = ui

      @if (in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
        ui.initOAuth({
          usePkceWithAuthorizationCodeGrant:
            "{!! (bool) config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
        })
      @endif
    }
  </script>
</body>

</html>
