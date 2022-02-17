<!DOCTYPE html>
<html>
  
<head>
  <title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
  <div class="row">        
    <div class="col">
         
      @if (Session::has('message'))
   <div class="alert alert-info" class="mb-3">{{ Session::get('message') }}</div>
     @endif
     @if (Auth::check())
     <h3 class="mb-3 text-center">Welcome {{Auth::user()->name}} (Admin) to Admin Dashboard!!</h3>
     @endif
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>