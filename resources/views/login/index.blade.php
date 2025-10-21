
<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <meta charset="utf-8">
      <link rel="stylesheet" href="style.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
      <style>
        /* Alert Styles */
.alert {
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 4px;
    font-size: 16px;
    line-height: 1.5;
    position: relative;
    border: 1px solid transparent;
    z-index: 9999; /* Ensures it's on top */
    width: 100%;
    max-width: 800px; /* Limits the alert width */
    margin: 0 auto; /* Centers the alert */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow for better visibility */
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.alert ul {
    padding-left: 20px;
}

.alert ul li {
    margin-bottom: 5px;
}

/* Optional: Close Button */
.alert .close {
    position: absolute;
    top: 10px;
    right: 15px;
    color: inherit;
    cursor: pointer;
    background: none;
    border: none;
    font-size: 20px;
}

.alert .close:hover {
    opacity: 0.7;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .alert {
        font-size: 14px;
        padding: 10px 15px;
        max-width: 90%; /* Make it more responsive on mobile */
    }
}

        button{
            background:grey;
            border:1px solid gray;
            color:#fff;
            padding:20px 70px;
            font-size:24px;
            position:relative;
            outline:none;
            cursor:pointer;
            min-width:300px;
            -webkit-border-radius:3px;
            -moz-border-radius
            border-radius:3px;
            -webkit-transition:background 0.2s ease-in-out;
            -moz-transition:background 0.2s ease-in-out;
            transition:background 0.2s ease-in-out;
            -webkit-box-shadow:0 2px 2px rgba(0,0,0,0.1);
            -moz-box-shadow:0 2px 2px rgba(0,0,0,0.1);
            box-shadow:0 2px 2px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.5);
        }
        button:not([disabled]):hover{
             background:#3a3a3a;
        }
        button[disabled]{
          background: #3a3a3a;
          color: #ffffff3b;
          cursor: default;
        }
        button:after{
            content:'';
            display:block;
            position:absolute;
            opacity:0;
            width:30px;
            height:30px;
            border:5px solid rgba(255,255,255,0.3);
            border-right-color:#fff;
            -webkit-border-radius:50%;
            -moz-border-radius:50%;
            border-radius:50%;
            left:-30px;
            top:15px;
            
            -webkit-transition-property: -webkit-transform;
            -webkit-transition-duration: .5s;
        
            -moz-transition-property: -moz-transform;
            -moz-transition-duration: .5s;
            
            -webkit-animation-name: rotate; 
            -webkit-animation-duration: .5s; 
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-timing-function: linear;
            
            -moz-animation-name: rotate; 
            -moz-animation-duration: .5s; 
            -moz-animation-iteration-count: infinite;
            -moz-animation-timing-function: linear;
            
            transition:all 0.2s linear;
            -webkit-transform:scale(2);
            transform:scale(2);
        }
        
        button.loading:after {
            opacity:1;
            left:15px;
        }
        
        @-webkit-keyframes rotate {
            from {-webkit-transform: rotate(0deg);}
            to {-webkit-transform: rotate(360deg);}
        }
        
        @-moz-keyframes rotate {
            from {-moz-transform: rotate(0deg);}
            to {-moz-transform: rotate(360deg);}
        }
        
        *{
          -webkit-box-sizing:border-box;
          -moz-box-sizing:border-box;
          box-sizing:border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            position: relative;
        }
        
        .slideshow-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .slideshow-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            opacity: 0;
            transition: opacity 2s ease-in-out;
        }
        
        .slideshow-container img.active {
            opacity: 1;
        }
        
        
        a{
          color:#7f8c8d;
        }
        
        .form-container{
          padding: 50px 40px;
          background:#fff;
          height: 550px;
          width:400px;
          text-align:center;
          -webkit-box-shadow:0 2px 3px rgba(0,0,0,0.2);
          -moz-box-shadow:0 2px 3px rgba(0,0,0,0.2);
          box-shadow:0 2px 3px rgba(0,0,0,0.2);
          margin:0 auto;
          -webkit-transition:all 1s linear;
          -moz-transition:all 1s linear;
          transition:all 1s linear;
          position:absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        }
        
        .form-container:after{
          content:"";
          display:block;
          position:absolute;
          top:0;
          left:0;
          width:100px;
          height:10px;
          background:#e74c3c;
          -webkit-box-shadow:100px 0 0 #e67e22, 200px 0 0 #f1c40f, 300px 0 0 #1abc9c;
          -moz-box-shadow:100px 0 0 #e67e22, 200px 0 0 #f1c40f, 300px 0 0 #1abc9c;
          box-shadow:100px 0 0 #e67e22, 200px 0 0 #f1c40f, 300px 0 0 #1abc9c;
        }
        
        .done .login-form{
          display:none;
        }
        
        .form-container .thank-msg{
          display:none;
        }
        
        .done .thank-msg{
          display:block;
        }
        
        .form-container h3{
          font-size:32px;
          text-align:center;
          color:#666;
          margin:0 0 30px;
        }
        
        .form-container .login-form > div{
          margin-bottom:20px;
        }
        
        .form-container .login-form > div > input{
          border:2px solid #dedede;
          padding:20px;
          font-size:20px;
          min-width:300px;
          color:#666;
          -webkit-border-radius:3px;
          -moz-border-radius:3px;
          border-radius:3px;
          outline:none;
          -webkit-transition:border-color 0.2s linear;
          -moz-transition:border-color 0.2s linear;
          transition:border-color 0.2s linear;
        }
        
        .form-container .login-form > div > input:focus{
          border-color:#A5A5A5;
        }
        
        .page-container{
          min-height: 500px;
        }
        
        .credits{
          text-align:center;
          color:#999;
          padding:10px;
        }

        .password-container {
            position: relative;
            display: inline-block;
         }

         .password-container input {
            width: 100%;
            padding-right: 40px; /* Make room for the eye icon */
         }

         .password-container .eye-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
         }

         .password-container .eye-icon:hover {
            color: #333;
         }
         
         </style>        
   </head>
   <body>
    <div class="container">
    @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
    @if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="slideshow-container">
  <img src="{{ asset('images/slideshow/foto1.jpg') }}" class="slideshow-image active" alt="Slideshow Image 1">
  <img src="{{ asset('images/slideshow/foto2.jpg') }}" class="slideshow-image" alt="Slideshow Image 2">
  <img src="{{ asset('images/slideshow/foto3.jpg') }}" class="slideshow-image" alt="Slideshow Image 3">
  <img src="{{ asset('images/slideshow/foto4.jpg') }}" class="slideshow-image" alt="Slideshow Image 4">
  <img src="{{ asset('images/slideshow/foto5.jpg') }}" class="slideshow-image" alt="Slideshow Image 5">
</div>
<div ng-app="App" class="page-container"> 
<form method="POST" action="{{ route('authenticate') }}">
    @csrf
    <div class="form-container" ng-class="done">
        <div class="login-form">
          <img src="{{ asset('images/logopadma.png') }}" alt="Logo" style="width: 200px;">
          <h3>Padma Business Monitoring</h3>
          <div>
            <input type="type" placeholder="Username" name="username" value="{{ old('username') }}" required>
         </div>

         <div class="password-container">
            <input type="password" placeholder="Password" name="password" id="password" required>
            <i class="fas fa-eye eye-icon" id="togglePassword"></i>
         </div>

         <div class="input-group">
            <button name="login" class="btn">Login</button>
         </div>
        </form>
     </div>
     <script>
      setInterval(function() {
          fetch("{{ route('session.refresh') }}").then(response => response.json());
      }, 60000); // 1 menit sekali
  </script>  
     <script>
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#password');

      togglePassword.addEventListener('click', function (e) {
          // toggle the type attribute
          const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
          password.setAttribute('type', type);
          
          // toggle the eye icon
          this.classList.toggle('fa-eye-slash');
      });
   </script>
     <script>
      let currentSlide = 0;
      const slides = document.querySelectorAll('.slideshow-container img');
  
      function showNextSlide() {
          slides[currentSlide].classList.remove('active');
          currentSlide = (currentSlide + 1) % slides.length;
          slides[currentSlide].classList.add('active');
      }
  
      setInterval(showNextSlide, 5000); // Change slide every 5 seconds
  </script>
  </body>
</html>