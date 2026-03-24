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

  <!-- Topbar overrides -->
  <style>
    html {
      /* Prevention of layout shifting */
      overflow-y: scroll;
    }

    .swagger-ui .topbar .wrapper .topbar-wrapper {
      justify-content: flex-end;
      flex-direction: row;
      align-items: center;
      height: 42px;

      a.link {
        display: none;
      }

      .download-url-wrapper {
        display: none;
      }
    }

    .swagger-ui .information-container {
      border-bottom: 1px solid #bbb;
      margin-bottom: 12px;
    }
  </style>

  <!-- Authorize section & button overrides -->
  <style>
    .swagger-ui .scheme-container {
      padding: 0;
      box-shadow: none !important;

      margin-top: -105px;
      margin-bottom: 32px;
      margin-left: auto;
      margin-right: calc((100vw - 1460px) / 2);

      position: relative;
      height: 67px;
      max-width: 250px;
      background-color: unset;

      @media screen and (max-width: 1460px) {
        margin-right: 0;
      }
    }

    .swagger-ui .dialog-ux {
      .modal-ux-content {
        text-align: center;

        .auth-btn-wrapper {
          gap: 16px;
        }

        .auth-container .wrapper {
          display: flex;
          justify-content: center;
          align-items: center;
          gap: 12px;
        }

        .auth-container label {
          margin-bottom: 0;
          font-size: 14px;
        }
      }
    }
  </style>

  <!-- Deprecated API -->
  <style>
    .swagger-ui .opblock.opblock-deprecated {
      opacity: 1;
      background: #eee;
      border-color: #bbb;
    }

    .swagger-ui .opblock.opblock-deprecated .opblock-summary-method {
      background: #ccc;
    }
  </style>

  <!-- (Removed) Response Section -->
  <style>
    .swagger-ui .response-col_links {
      display: none;
    }
  </style>

  <!-- API section (Not yet validated) -->
  <style>
    .renderedMarkdown {
      line-height: 1.25em;
    }

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

    .swagger-ui table thead tr td,
    .swagger-ui table thead tr th {
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
  </style>

  <!-- API counter styles -->
  <style>
    #api-stats {
      display:none;
      position: absolute;
      top: 160px;
      left: calc(50vw - 710px + 452px);
      font-family: sans-serif;

      ul {
        list-style: none;
        padding-left: 0;
        margin-block: 0;

        li {
          display: inline-block;
          padding: 2px 10px;
          border-radius: 99px;
          font-size: 14px;
          font-weight: bold;
          margin-right: 2px;
        }
      }
    }

    html {
      #api-stats ul li {
        color: #fff;

        &.all-api-summary { background-color: #8f50ff; }
        &.get-summary { background-color: #61affe; }
        &.post-summary { background-color: #49cc90; }
        &.put-summary { background-color: #fca130; }
        &.delete-summary { background-color: #f93e3e; }
      }

      &.dark-mode #api-stats ul li {
        color: #080a0b;

        &.all-api-summary { background-color: #7c46dd; }
        &.get-summary { background-color: #55a1ff; }
        &.post-summary { background-color: #00b572; }
        &.put-summary { background-color: #ff7d35; }
        &.delete-summary { background-color: #eb6156; }
      }

    }
  </style>

  <!-- Search Bar -->
  <style>
    .custom-search-wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      position: relative;

      .custom-search-input {
        width: 100%;
        padding: 6px 10px 6px 35px !important;
        outline: none;
        color: #3b4151;
        max-width: 600px;
      }

      .search-icon {
        position: absolute;
        left: 10px;
        width: 18px;
        height: 18px;
        pointer-events: none;
      }
    }

    html.dark-mode {
      .custom-search-wrapper {
        .custom-search-input {
          border: 1px solid #62a03f !important;
        }

        .search-icon {
          color: #62a03f;
        }
      }
    }

    .opblock.filter-hidden,
    .opblock-tag-section.filter-hidden {
      display: none !important;
    }
  </style>
</head>

<body>
  <div id="api-stats">
    <ul>
      <li class="all-api-summary">All APIs: <span id="api-count-all">0</span></li>
      <li class="get-summary">GET : <span id="api-count-get">0</span></li>
      <li class="post-summary">POST : <span id="api-count-post">0</span></li>
      <li class="put-summary">PUT : <span id="api-count-put">0</span></li>
      <li class="delete-summary">DELETE : <span id="api-count-delete">0</span></li>
    </ul>
  </div>

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

        onComplete: function() {

          /* ========== BLOCK :: API COUNTER ==========*/

          const specImmutable = ui.specSelectors.specJson();
          if (!specImmutable) return;

          const spec = specImmutable.toJS();
          if (!spec.paths || Object.keys(spec.paths).length === 0) return;

          const paths = spec.paths;
          let counts = { all: 0, get: 0, post: 0, put: 0, delete: 0 };

          for (let path in paths) {
            for (let method in paths[path]) {
              const m = method.toLowerCase();
              counts.all++;

              if (counts[m] !== undefined) counts[m]++;
            }
          }

          document.getElementById('api-count-all').innerText = counts.all;
          document.getElementById('api-count-get').innerText = counts.get;
          document.getElementById('api-count-post').innerText = counts.post;
          document.getElementById('api-count-put').innerText = counts.put;
          document.getElementById('api-count-delete').innerText = counts.delete;

          // Done loading, show the statistics container now
          document.getElementById('api-stats').style.display = 'block';

          /* =============== END BLOCK ================*/


          /* ========== BLOCK :: SEARCH BAR ==========*/

          // Inject input before dark mode toggle
          const topbarWrapper = document.querySelector('.topbar-wrapper');
          const darkModeToggle = document.querySelector('.dark-mode-toggle');

          if (topbarWrapper && darkModeToggle) {
            const searchHtml = `
              <div class="custom-search-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="search-icon">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" id="api-search-bar" class="custom-search-input" placeholder="Search descriptions (e.g. 'login')...">
              </div>
            `;

            darkModeToggle.insertAdjacentHTML('beforebegin', searchHtml);
          }

          // Handle search behavior
          const searchBar = document.getElementById('api-search-bar');

          if (searchBar) {
            searchBar.addEventListener('input', function() {
              const query = this.value.toLowerCase();
              const operations = document.querySelectorAll('.opblock');

              operations.forEach(op => {
                const descEl = op.querySelector('.opblock-summary-description');
                const description = descEl ? descEl.textContent.toLowerCase() : "";

                if (description.includes(query)) {
                  op.classList.remove('filter-hidden');
                } else {
                  op.classList.add('filter-hidden');
                }
              });

              // Hide headers that have no endpoints after query
              document.querySelectorAll('.opblock-tag-section').forEach(section => {
                const visibleOps = section.querySelectorAll('.opblock:not(.filter-hidden)').length;

                if (query !== "" && visibleOps === 0) {
                  section.classList.add('filter-hidden');
                } else {
                  section.classList.remove('filter-hidden');
                }
              });
            });
          }

          /* =============== BLOCK END =============== */
        }
      })

      window.ui = ui;

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
