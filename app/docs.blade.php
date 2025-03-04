<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />

  <title>
    {{ config('l5-swagger.documentations.' . $documentation . '.api.title') }}
  </title>

  <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}" />

  <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32" />
  <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}" sizes="16x16" />

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

    .renderedMarkdown {
      line-height: 1.25em;
    }

    .swagger-ui .info {
      margin-bottom: 0;
    }

    .swagger-ui .scheme-container {
      padding: 0;
      box-shadow: none;
      background: none;

      margin-top: -85px;
      margin-bottom: 12px;
      margin-left: auto;
      margin-right: calc((100vw - 1460px) / 2);

      position: relative;
      height: 67px;
      max-width: 250px;

      @media screen and (max-width: 1460px) {
        margin-right: 0;
      }
    }

    .swagger-ui .scheme-container .schemes {
      position: absolute;
      right: 0;
      width: auto;
    }

    .swagger-ui .topbar .topbar-wrapper {
      gap: 32px;
    }

    .swagger-ui .topbar .topbar-wrapper .link {
      flex-grow: unset;
    }

    .swagger-ui .topbar .topbar-wrapper .download-url-wrapper {
      flex-grow: 1;
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

    .swagger-ui .execute-wrapper,
    .swagger-ui .opblock-body .btn-group {
      padding-top: 0;
      padding-bottom: 0;
    }

    .swagger-ui .execute-wrapper .btn.execute,
    .swagger-ui .opblock-body .btn-group .btn.execute,
    .swagger-ui .opblock-body .btn-group .btn.btn-clear {
      margin-bottom: 20px;
    }

    .swagger-ui table thead tr td, .swagger-ui table thead tr th {
      font-size: 14px;
    }

    .swagger-ui table tbody tr td.parameters-col_name:first-of-type {
      padding-right: 12px;
    }

    .swagger-ui table.parameters .parameters-col_description div p {
      margin-bottom: 8px;
    }

    .swagger-ui table.parameters .parameters-col_description div:not(:first-of-type) p {
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

  <link rel="stylesheet" href="./swagger-dark.min.css" media="(prefers-color-scheme: dark)" />

  <!-- Light mode overrides -->
  <style>
    .swagger-ui .scheme-container .schemes .auth-wrapper .btn.authorize:not(.modal-btn) {
      align-items: center;
      padding: 8px 21px;
    }

    .swagger-ui .scheme-container .schemes .auth-wrapper .btn.authorize span {
      float: unset;
      padding: 0 12px 0 0;
    }

    .swagger-ui .scheme-container .schemes .auth-wrapper .btn.authorize svg {
      width: 16px;
      height: 16px;
    }

    .swagger-ui .auth-btn-wrapper {
      gap: 16px;
    }

    .swagger-ui .btn:hover {
      opacity: 0.75;
    }
  </style>

  <!-- Dark mode overrides -->
  <style>
    @media (prefers-color-scheme: dark) {
      html, body {
        background: #121212;
      }

      ::-webkit-scrollbar {
        width: unset;
        height: unset;
      }

      .swagger-ui .scheme-container {
        background: #121212;
      }

      .swagger-ui .btn:hover {
        opacity: 0.8;
      }

      .swagger-ui .checkbox p, .swagger-ui .dialog-ux .modal-ux-content h4, .swagger-ui .dialog-ux .modal-ux-content p, .swagger-ui .dialog-ux .modal-ux-header h3, .swagger-ui .errors-wrapper .errors h4, .swagger-ui .errors-wrapper hgroup h4, .swagger-ui .info .base-url, .swagger-ui .info .title, .swagger-ui .info h1, .swagger-ui .info h2, .swagger-ui .info h3, .swagger-ui .info h4, .swagger-ui .info h5, .swagger-ui .info li, .swagger-ui .info p, .swagger-ui .info table, .swagger-ui .loading-container .loading::after, .swagger-ui .model, .swagger-ui .opblock .opblock-section-header h4, .swagger-ui .opblock .opblock-section-header > label, .swagger-ui .opblock .opblock-summary-description, .swagger-ui .opblock .opblock-summary-operation-id, .swagger-ui .opblock .opblock-summary-path, .swagger-ui .opblock .opblock-summary-path__deprecated, .swagger-ui .opblock-description-wrapper, .swagger-ui .opblock-description-wrapper h4, .swagger-ui .opblock-description-wrapper p, .swagger-ui .opblock-external-docs-wrapper, .swagger-ui .opblock-external-docs-wrapper h4, .swagger-ui .opblock-external-docs-wrapper p, .swagger-ui .opblock-tag small, .swagger-ui .opblock-title_normal, .swagger-ui .opblock-title_normal h4, .swagger-ui .opblock-title_normal p, .swagger-ui .parameter__name, .swagger-ui .parameter__type, .swagger-ui .response-col_links, .swagger-ui .response-col_status, .swagger-ui .responses-inner h4, .swagger-ui .responses-inner h5, .swagger-ui .scheme-container .schemes > label, .swagger-ui .scopes h2, .swagger-ui .servers > label, .swagger-ui .tab li, .swagger-ui label, .swagger-ui select, .swagger-ui table.headers td {
        color: #fff;
      }

      .swagger-ui .opblock-tag {
        border-bottom-color: #616161;
      }

      .swagger-ui .opblock.opblock-get .opblock-summary-method, .swagger-ui .opblock.opblock-get .tab-header .tab-item.active h4 span::after {
        background: #1E88E5;
      }

      .swagger-ui .opblock.opblock-post .opblock-summary-method, .swagger-ui .opblock.opblock-post .tab-header .tab-item.active h4 span::after {
        background: #4CAF50;
      }

      .swagger-ui .opblock.opblock-put .opblock-summary-method, .swagger-ui .opblock.opblock-put .tab-header .tab-item.active h4 span::after {
        background: #F57C00;
      }

      .swagger-ui .opblock.opblock-delete .opblock-summary-method, .swagger-ui .opblock.opblock-delete .tab-header .tab-item.active h4 span::after {
        background: #D32F2F;
      }

      .swagger-ui .opblock.opblock-get,
      .swagger-ui .opblock.opblock-post,
      .swagger-ui .opblock.opblock-put,
      .swagger-ui .opblock.opblock-delete {
        background: none;
      }

      .swagger-ui .opblock.opblock-get .opblock-summary {
        background-color: rgba(42, 105, 167, .1);
      }

      .swagger-ui .opblock.opblock-get .opblock-body {
        background-color: rgba(42, 105, 167, .3);
        color: #fff;
      }
      .swagger-ui .opblock.opblock-post .opblock-summary {
        background-color: rgba(72, 203, 144, .1);
      }

      .swagger-ui .opblock.opblock-post .opblock-body {
        background-color: rgba(72, 203, 144, .3);
        color: #fff;
      }

      .swagger-ui .opblock.opblock-put .opblock-summary {
        background-color: rgba(213, 157, 88, .1);
      }

      .swagger-ui .opblock.opblock-put .opblock-body {
        background-color: rgba(213, 157, 88, .3);
        color: #fff;
      }

      .swagger-ui .opblock.opblock-put .opblock-summary {
        background-color: rgba(200, 50, 50, .1);
      }

      .swagger-ui .opblock.opblock-put .opblock-body {
        background-color: rgba(200, 50, 50, .3);
        color: #fff;
      }

      .swagger-ui .opblock-body pre.microlight, .swagger-ui textarea.curl {
        background: #212121 !important;
      }

      .swagger-ui .opblock-control-arrow,
      .swagger-ui .authorization__btn {
        fill: #fff;
      }

      .swagger-ui .btn.try-out__btn {
        background-color: #388E3C;
        border-color: #2E7D32;
      }

      .swagger-ui .opblock-deprecated .btn.try-out__btn {
        background-color: #546E7A;
        border-color: #37474F;
      }

      .swagger-ui .btn.try-out__btn,
      .swagger-ui .btn.try-out__btn.cancel {
        color: #fff;
        width: 120px;
      }

      .swagger-ui .btn.authorize, .swagger-ui .btn.cancel {
        background-color: #D32F2F;
        border-color: #C62828;
        color: #fff;
      }

      .swagger-ui .btn.execute {
        background-color: #388E3C;
        border-color: #2E7D32;
      }

      .swagger-ui .opblock-deprecated .btn.execute {
        background-color: #546E7A;
        border-color: #37474F;
      }

      .swagger-ui .dialog-ux .modal-ux-header .close-modal {
        fill: #fff;
      }
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
        displayRequestDuration: true,
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
