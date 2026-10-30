<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="{{ route('movies') }}" method="get">
        <input type="search" name="search" placeholder="Search movies" value="{{ request('search') }}" required>
        <input type="submit" value="Go">
      </form>
    </div>
    <div class="w3l_sign_in_register">
      <ul>
        @auth
          @if (strtolower(auth()->user()->role?->name ?? '') === 'admin')
            <li><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
          @endif
          <li>
            <form action="{{ route('logout') }}" method="post">
              @csrf
              <button type="submit">Logout ({{ auth()->user()->name }})</button>
            </form>
          </li>
        @else
          <li><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li>
        @endauth
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>