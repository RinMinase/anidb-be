<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link
      href="https://fonts.googleapis.com/css?family=Nunito:200"
      rel="stylesheet"
      type="text/css"
    />

    <link rel="icon" href="data:;base64,=" />
    <title>Rin's AniDB API</title>

    <style>
      body {
        margin: 0;
        color: #636b6f;
        font-family: "Nunito", sans-serif;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .nav {
        width: 100%;
        font-size: 18px;
        position: fixed;
        top: 0;
        display: flex;
        justify-content: space-between;
        padding: 10px;
      }
      .nav .item {
        margin: 0 10px;
        padding: 10px;
        font-weight: bold;
        cursor: pointer;
      }
      .nav .item.source {
        margin-left: auto;
      }
      .nav .item:hover {
        background-color: #eee;
        border-radius: 8px;
        color: #222;
      }
      .nav .item a {
        color: inherit;
        text-decoration: none;
      }
      .title {
        text-align: center;
        font-size: 84px;
        margin: 0 8px;
      }
      @media (max-width: 576px) {
        .title {
          font-size: 56px;
        }
      }
      .subtitle {
        font-size: 18px;
        margin-bottom: 30px;
        position: fixed;
        text-align: center;
        bottom: 0;
      }
      .subtitle p {
        margin: 6px 0;
      }
    </style>
  </head>

  <body>
    <div class="nav">
      @if($isProd == false)
        <p class="item">
          <a href="/docs">Docs</a>
        </p>
      @endif
      <p class="item source">
        <a
          href="https://github.com/RinMinase/anidb-be"
          target="_blank"
          rel="noopener"
          >Source</a
        >
      </p>
    </div>

    <div class="title">Rin's AniDB API / Middleware</div>
    <div class="subtitle">
      <p>Laravel Framework v{{ app()->version(); }}</p>
      <p>PHP v{{ phpversion(); }}</p>
    </div>
  </body>
</html>
