<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Health') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <style>
      * {
        font-family: 'Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif', "Apple Color Emoji", "Segoe UI Emoji";
      }

      body {
        -webkit-font-smoothing: 'antialiased';
      }

      @keyframes ping {
        75%, 100% {
          transform: scale(2);
          opacity: 0;
        }
      }

      .container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100svh;
        width: 100vw;
        user-select: none;
      }

      .sub-container {
        width: 100%;
        max-width: 700px;
        padding: 12px;
      }

      .text-box {
        padding: 16px 24px;
        background-color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        box-shadow:
        0 0 #0000, 0 0 #0000, 0 0 #0000, 0 0 #0000, 0 25px 50px -12px rgb(107 114 128 / 0.2);
      }

      .indicator {
        position: relative;
        display: flex;
        height: 16px;
        width: 16px;

        .inner {
          position: relative;
          display: inline-flex;
          height: 16px;
          width: 16px;
          background-color: rgb(74 222 128);
          border-radius: 99px;
        }

        .outer {
          position: absolute;
          display: inline-flex;
          width: 100%;
          height: 100%;
          background-color: rgb(74 222 128);
          border-radius: 99px;
          opacity: 0.75;

          animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        &.status-down .inner,
        &.status-down .outer {
          background-color: rgb(220 38 38);
        }
      }

      .text-container {
        margin-left: 24px;

        h2 {
          font-size: 20px;
          line-height: 28px;
          font-weight: 600;
          color: rgb(17 24 39);
          margin: 0
        }

        p {
          margin-top: 8px;
          margin-bottom: 0;
          font-size: 14px;
          line-height: 1.625;
          color: rgb(107 114 128)
        }
      }

      @media (prefers-color-scheme: dark) {
        body {
          background-color: #212121;
          color: #fff;
        }

        .text-box {
          background-color: #1a1a1a;
        }

        .text-container {
          h2 {
            color: #b0bec5;
          }

          p {
            color: #cfd8dc;
          }
        }
      }
    </style>
</head>
<body>
  <div class="container">
    <div class="sub-container">
      <div class="text-box">
        <div class="indicator {{ $exception ? 'status-down' : null }}">
            <span class="outer"></span>
            <span class="inner"></span>
        </div>

        <div class="text-container">
          <h2>Application {{ $exception ? 'experiencing problems' : 'up' }}</h2>

          <p>
            HTTP request received.

            @if (defined('LARAVEL_START'))
                Response rendered in {{ round((microtime(true) - LARAVEL_START) * 1000) }}ms.
            @endif
          </p>
        </div>
      </div>
    </div>
</div>
</body>
</html>
