<!DOCTYPE html>
<head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container">
         @include('component.nav')
    @if (Session::has('message'))
   <div class="alert alert-info" class="mb-3">{{ Session::get('message') }}</div>
    @endif
    <div class="row">        
    <div class="col-md-4 offset-md-4">
    <h3 class="text-center">{{auth('admin')->user()->name}} (Admin)</h3>
    <h3 class="text-center">Please Verify Your Account with New Email Verification Link!</h3>
  
   <form action="{{route('admin-verify-email-resend')}}" method="post">
    @csrf
    <div class="text-center mt-5">
    <button type="submit" class="btn btn-info">Resend Verification Email</button>
    </div>
</form>

    @include('component.logout')
    </div>
    </div>
    </div>
   <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>