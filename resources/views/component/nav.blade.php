 <div class="row">        
    <div class="col">
<ul class="nav nav-pills mt-1">

   @if(Auth::guard('admin')->check())
   <li class="nav-item">
    <a class="nav-link" href="{{route('home')}}">Home</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{route('dashboard-admin')}}">Admin Dashboard</a>
  </li>
   <li class="nav-item">
    <a class="nav-link" href="{{route('setting-admin')}}">Admin Setting</a>
  </li>
  @endif

  @if(Auth::guard('blogger')->check())
<li class="nav-item">
    <a class="nav-link" href="{{route('home')}}">Home</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{route('dashboard-blogger')}}">Blogger Dashboard</a>
  </li>
   <li class="nav-item">
    <a class="nav-link" href="{{route('setting-blogger')}}">Blogger Setting</a>
  </li>
  @endif

  @if(!Auth::guard('admin')->check() && !Auth::guard('blogger')->check())
  <li class="nav-item">
    <a class="nav-link" href="{{route('home')}}">Home</a>
  </li>
    <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Sign Up</a>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="{{route('register-admin')}}">Admin</a>
      <a class="dropdown-item" href="{{route('register-blogger')}}">Blogger</a>
    </div>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Log In</a>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="{{route('login-admin')}}">Admin</a>
      <a class="dropdown-item" href="{{route('login-blogger')}}">Blogger</a>
    </div>
  </li>
  @endif

</ul>
</div>
</div>