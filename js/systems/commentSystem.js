function addComment(target) {
    loader.active();

    $.ajax({
        url: "php/commentSystem.php",
        method: "post",
        data: "type=add&"+$(target.parentElement).serialize(),
        success: commentId => {
            var data = $(target.parentElement).serializeArray();
            
            $("#comments-container-"+parseInt(data[0].value)).prepend(`
            <div class="col-12 pl-5 mt-3" id="comment-${parseInt(data[0].value)}-${commentId}">
                <div class="comment-profile-container">
                    <a class="h6" href="User/${data[2].value}">${data[2].value}</a>
                    <span class="float-right">Hace un momento.</span><br>
                    <span class="content">${data[1].value}</span>
                    <button class="btn btn-link p-0 delete" onclick="deleteComment(${commentId}, ${data[0].value})">
                        Eliminar
                    </button>
                </div>
            </div>
            `);

            loader.desactive();
        },
    });
}

function deleteComment(commentId, publicationId) {
    if(!confirm("¿Deseas borrar este comentario?")) {
        return;
    }

    loader.active();

    $.ajax({
        url: "php/commentSystem.php",
        method: "post",
        data: "type=delete&commentId="+commentId,
        success: () => {
            $(`#comment-${publicationId}-${commentId}`).remove();

            loader.desactive();
        }
    });
}



var commentsOffet = [];

function chargeMoreComments(publicationId) {
    loader.active();

    if(!commentsOffet[publicationId]) {
        commentsOffet[publicationId] = 0;
    }
    commentsOffet[publicationId] += 10;

    $.ajax({
        url: "php/commentSystem.php",
        method: "post",
        data: `type=get&publicationId=${publicationId}&limit=10&offset=${commentsOffet[publicationId]}`,
        success: comments => {
            comments = JSON.parse(comments);

            comments.forEach(comment => {
                $("#comments-container-"+publicationId).append(`
                <div class="col-12 pl-5 mt-3" id="comment-${publicationId}-${comment.id}">
                    <div class="comment-profile-container">
                        <a class="h6" href="User/${comment.username}">${comment.username}</a>
                        <span class="float-right">Hace un momento.</span><br>
                        <span class="content">${comment.content}</span>
                        <button class="btn btn-link p-0 delete" onclick="deleteComment(${comment.id}, ${publicationId})">
                            Eliminar
                        </button>
                    </div>
                </div>
                `);
            });

            if(comments.length < 10) {
                $("#charge-comments-button-"+publicationId).remove();
            }

            loader.desactive();
        }
    })
}