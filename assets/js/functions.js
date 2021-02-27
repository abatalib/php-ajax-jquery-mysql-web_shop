$().ready(function(){
    $(document).on('click', '#resend', function(){
        let mail = $(this).attr('data');
        $.ajax({
            url:"ajax-scripts",
            type:"post",
            data:{source:'resend-mail', mail},
            success:function(rep){
                
                if(rep=='ok')
                {
                    alert("Courriel d'activation est envoyé");
                } else if(rep=='NA') {
                    alert("Pas d'adresse courriel passée")
                } else {
                    alert("Erreur lors d'envoi du courriel d'activation");
                }

                window.location.replace("connexion");
            }
        });
    });
});