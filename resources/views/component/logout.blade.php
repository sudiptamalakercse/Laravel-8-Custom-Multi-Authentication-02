<form action="{{route('logout')}}" method="post">
@csrf
<p class=" text-center">
    <button type="submit" class="btn btn-danger mt-5">Log Out</button>
</p>
</form>