{{-- <header class="banner">
  <div class="container">
    <a class="brand" href="{{ home_url('/') }}">{{ get_bloginfo('name', 'display') }}</a>
    <nav class="nav-primary">
      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
      @endif
    </nav>
  </div>
</header> --}}
<header>
  <div class="container">
    <div class="d-lg-flex flex-row align-items-end mb-1 mb-lg-2">
      <div class="col-lg-4">
        <a class="brand no-decoration" href="{{ home_url('/') }}">
          <span class="h2 m-0 text-green-flower">{{ get_bloginfo('name', 'display') }}</span>
        </a>
      </div>
      <div class="col-lg-8">
        <div class="row">
          <div class="col-12 col-lg-4 px-2">
            @if (has_nav_menu('auth_menu'))
              {!! wp_nav_menu([
                'theme_location' => 'auth_menu',
                'menu_class' => 'nav justify-content-center auth-menu py-2 py-lg-0',
                'container'       => 'ul',
                'container_id'    => 'authMenu',
              ]) !!}
            @endif
            <article class="border">
                <div class="px-2 border-bottom">
                  <div class="dropleft">
                      <span class="dropdown-toggle" id="navbarCar" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <small>Dropdown</small>
                      </span>
                      <div class="dropdown-menu" aria-labelledby="navbarCar">
                        <div class="mini-cart">
                          <div class="car-content">
                            <article>
                              <div class="d-flex justify-content-around align-items-center">
                                <figure>
                                  <img src="http://localhost:3000/wp-content/uploads/2018/08/descarga.jpg" class="img-fluid w-100px">
                                </figure>
                                <h2 class="h7 mw-150px">Sweater Criminal</h2>
                                <p class="mb-2 h7 mw-100px"><strong>1 x 1,568.00 $.</strong></p>
                              </div>
                            </article>
                          </div>
                          <div class="cart-total bg-light">
                            <div class="row">
                              <div class="options-cart col">
                                <ul class="list-unstyled p-3 mb-0 justify-content-center">
                                  <li class="text-center my-2">
                                    <a class="text-white bg-gray-flower p-1 no-decoration" href="#">Shop Cart</a>
                                  </li>
                                  <li class="text-center my-2">
                                    <a class="text-white bg-green-flower p-1 no-decoration" href="#">Checkout</a>
                                  </li>
                                </ul>
                              </div>
                              <div class="col">
                                <table class="table table-borderless table-sm mb-0">
                                  <tbody>
                                    <tr>
                                      <td><strong>Sub-Total:</strong></td>
                                      <td>1,568.00 $</td>
                                    </tr>
                                    <tr>
                                      <td><strong>IVA:</strong></td>
                                      <td>168.00 $</td>
                                    </tr>
                                    <tr>
                                      <td><strong>Total:</strong></td>
                                      <td>1,568.00 $</td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
                <div class="px-2 py-1 d-flex">
                  <span class="mr-auto">C</span>
                  <span class="px-1">
                    <small>0 ITEMS</small>
                  </span>
                  <span class="px-1 border-left">
                    <small>0 USD</small>
                  </span>
                </div>
            </article>
          </div>
          @include('partials.searchform')
        </div>
      </div>
    </div>
  </div>
  <nav class="navbar navbar-expand-lg navbar-dark bg-green-flower">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarPrimary" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    @if (has_nav_menu('primary_navigation'))
      {!! wp_nav_menu([
        'theme_location' => 'primary_navigation',
        'menu_class' => 'navbar-nav mx-auto text-center',
        'container'       => 'div',
        'container_class' => 'collapse navbar-collapse',
        'container_id'    => 'navbarPrimary',
      ]) !!}
    @endif
  </nav>
</header>
