<script>
    window.reservation_id=@js(request()->route()->parameter("record"));
    window.token = @js(auth()->user()?->createToken("site-token")?->plainTextToken);
</script>
