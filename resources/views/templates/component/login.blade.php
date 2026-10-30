<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Sign In & Sign Up
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
      </div>
      <section>
        <div class="modal-body">
          <div class="w3_login_module">
            <div class="module form-module">
              <div class="toggle"><i class="fa fa-times fa-pencil"></i>
              <div class="tooltip">Click Me</div>
              </div>
              <div class="form">
              <h3>Login to your account</h3>
              <form action="{{ route('login.store') }}" method="post">
                @csrf
                <input type="hidden" name="auth_form" value="login">
                <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" autocomplete="username" required>
                <input type="password" name="password" placeholder="Password" autocomplete="current-password" required>
                @error('username')
                  <p role="alert" style="color: #c00">{{ $message }}</p>
                @enderror
                @error('password')
                  <p role="alert" style="color: #c00">{{ $message }}</p>
                @enderror
                <input type="submit" value="Login">
              </form>
              </div>
              <div class="form">
              <h3>Create an account</h3>
              <form action="{{ route('register.store') }}" method="post">
                @csrf
                <input type="hidden" name="auth_form" value="register">
                <input type="text" name="username" placeholder="Username" value="{{ old('auth_form') === 'register' ? old('username') : '' }}" autocomplete="username" required>
                @error('username')
                  <p role="alert" style="color: #c00">{{ $message }}</p>
                @enderror
                <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" autocomplete="email" required>
                @error('email')
                  <p role="alert" style="color: #c00">{{ $message }}</p>
                @enderror
                <input type="password" name="password" placeholder="Password (minimum 8 characters)" autocomplete="new-password" required>
                @error('password')
                  <p role="alert" style="color: #c00">{{ $message }}</p>
                @enderror
                <input type="password" name="password_confirmation" placeholder="Confirm Password" autocomplete="new-password" required>
                <input type="number" name="umur" placeholder="Umur" min="1" max="120" value="{{ old('umur') }}" required>
                @error('umur')
                  <p role="alert" style="color: #c00">{{ $message }}</p>
                @enderror
                <input type="text" name="alamat" placeholder="Alamat" value="{{ old('alamat') }}" required>
                @error('alamat')
                  <p role="alert" style="color: #c00">{{ $message }}</p>
                @enderror
                <textarea name="bio" placeholder="Bio" required>{{ old('bio') }}</textarea>
                @error('bio')
                  <p role="alert" style="color: #c00">{{ $message }}</p>
                @enderror
                <input type="submit" value="Register">
              </form>
              </div>
              <div class="cta"><a href="#">Forgot your password?</a></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<script>
  $('.toggle').click(function(){
    // Switches the Icon
    $(this).children('i').toggleClass('fa-pencil');
    // Switches the forms  
    $('.form').animate({
    height: "toggle",
    'padding-top': 'toggle',
    'padding-bottom': 'toggle',
    opacity: "toggle"
    }, "slow");
  });
</script>
@if ($errors->any())
  @push('script')
    $('#myModal').modal('show');
    @if (old('auth_form') === 'register')
      $('.toggle').trigger('click');
    @endif
  @endpush
@endif