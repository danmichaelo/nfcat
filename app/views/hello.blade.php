<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Near Field Cat</title>
    <style>
        @import url(//fonts.googleapis.com/css?family=Lato:300,400,700);

        body {
            margin:0;
            font-family:'Lato', sans-serif;
            text-align:center;
            color: #999;
        }

        .welcome {
           width: 440px;
           height: 400px;
           position: absolute;
           left: 50%;
           top: 50%; 
           margin-left: -220px;
           margin-top: -200px;
        }

        a, a:visited {
            color:#FF5949;
            text-decoration:none;
        }

        a:hover {
            text-decoration:underline;
        }

        ul li {
            display:inline;
            margin:0 1.2em;
        }

        p {
            margin:2em 0;
            color:#555;

        }
        h1 {
            text-shadow: red 2px -4px 2px;
        }
        .cat {
            color: blue;
                display:inline-block;
                transform:scale(1.5,1); /* W3C */
                -webkit-transform:scale(1.5,1); /* Safari and Chrome */
                -moz-transform:scale(1.5,1); /* Firefox */
                -ms-transform:scale(1.5,1); /* IE 9 */
                -o-transform:scale(1.5,1); /* Opera */
        }

        /*
        * {
            -webkit-transition: all 100ms ease-out .5s;
            -moz-transition: all 100ms ease-out .5s;
            -o-transition: all 100ms ease-out .5s;
            transition: all 100ms ease-out .5s;
        }
        */

        img {
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="welcome">
        <img src="/img/nfcat.jpg">

        {{ Config::get('ncip::url') }}
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script> 
    <script type="text/javascript">
        setInterval(function() {
            $('h1 span').toggleClass('cat');
        },500);
    </script>
</body>
</html>
