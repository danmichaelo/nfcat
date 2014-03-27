<!DOCTYPE html>
<html lang="nb">
<head>
  <title>NFCAT</title>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
  <!-- Complete CSS (Responsive, With Icons) -->
  <!--
  <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.min.css">
-->
  <link rel="stylesheet" type="text/css" href="/site.css">

</head>
<body>

  <div class="container" style="display:none;">

    @section('sidebar')

    {{--

    <div class="navbar">
      <ul class="nav navbar-nav">
        <li><a href="{--{ URL::action('LoansController@getIndex') }--}">Mine lån</a></li>
        <li><a href="{--{ URL::action('UsersController@getIndex') }--}">Skann en bok</a></li>
      </ul>
     </div>
     --}}
    
    @show

    @if (!empty($status))
      <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>  
        {{$status}}
      </div>
    @endif

    @yield('content')

  </div>

  <script type="text/javascript" src="/components/messageformat/messageformat.js"></script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script> 
  <!--
  <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="/hogan-2.0.0.js"></script>
  <script type="text/javascript" src="/typeahead.js/typeahead.min.js"></script>
  --!
  <!--
  <script src="//cdnjs.cloudflare.com/ajax/libs/css3finalize/3.4.0/jquery.css3finalize.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.0.0/backbone-min.js"></script>
  -->
  @yield('scripts')

<!--
  <script type="text/javascript">
    if (window.location.href.match(/loans/)) {
      $('.navbar li:nth-child(1)').addClass('active');
    } else if (window.location.href.match(/users/)) {
      $('.navbar li:nth-child(2)').addClass('active');
    } else if (window.location.href.match(/documents/)) {
      $('.navbar li:nth-child(3)').addClass('active');
    } else if (window.location.href.match(/things/)) {
      $('.navbar li:nth-child(4)').addClass('active');
    }
  </script>
 -->
</body> 
</html>
