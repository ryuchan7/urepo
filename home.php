
<style>
    #main-header{
        height:50vh;
    }
</style>

<script>
    $(function(){
        $('#search-frm').submit(function(e){
            e.preventDefault();
            location.href = "./?"+$(this).serialize()
        })
    })

</script>