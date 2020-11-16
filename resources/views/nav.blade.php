<nav class="navbar navbar-expand navbar-dark blue-gradient" style="width:100%;position:fixed;z-index:100;top:0">

  <a class="navbar-brand" href="/"><i class="far fa-sticky-note mr-1"></i>memo</a>

  <ul class="navbar-nav ml-auto">

    @guest
    <li class="nav-item">
    </li>
    @endguest

    @guest
    <li class="nav-item">
    </li>
    @endguest

    @auth
    <li class="nav-item">
    </li>
    @endauth

    @auth
    <!-- Dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
         aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user-circle"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right dropdown-primary" aria-labelledby="navbarDropdownMenuLink">
<!--       
           <button class="dropdown-item" type="button"
                onclick="location.href='{{ route("users.show", ["name" => Auth::user()->name]) }}'">
          マイページ
        </button> 
      -->
        <div class="dropdown-divider"></div>
        <button form="logout-button" class="dropdown-item" type="submit">
          ログアウト
        </button>
      </div>
    </li>
    <!-- <form id="logout-button" method="POST" action="{{ route('logout') }}">
      @csrf
    </form> -->
    <!-- Dropdown -->
    @endauth

  </ul>

</nav>
