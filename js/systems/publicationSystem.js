//Add publication
var hasImage = false;

$("#img").change(e => {
    const reader = new FileReader();

    reader.onload = e => {
        $("#add-image-label").addClass("d-none");
        $("#preview-container").removeClass("d-none");
        $("#img-preview").attr("src", e.target.result);
        hasImage = true;
    };

    reader.readAsDataURL(document.getElementById("img").files[0]);
});

$("#quit-image").click(e => {
    $("#add-image-label").removeClass("d-none");
    $("#preview-container").addClass("d-none");
    hasImage = false;
});

$("#publication-form").submit(e => {
    e.preventDefault();
    
    loader.active();

    const data = new FormData($("#publication-form")[0]);
    data.append("type", "add");
    if(hasImage) {
        data.append("hasImage", "ok");
    }else{
        data.append("hasImage", "no");
    }

    $.ajax({
        url: "php/publicationSystem.php",
        method: "post",
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        success: data => {
            loader.desactive();

            data = JSON.parse(data);

            if(data.state) {
                window.location.reload();
            }else{
                alerts.add("error", data.msg);
            }
        },
    });
});

//Delete publication

function deletePublication(id) {
    if(!confirm("¿Desea eliminar esta publicacion?")) {
        return;
    }

    loader.active();

    $.ajax({
       url: "php/publicationSystem.php",
       method: "post",
       data: "type=delete&id="+id,
       success: data => {
           data = JSON.parse(data);

           if(data.state) {
               alerts.add("success", data.msg);
           }

           $("#publication-"+id).remove();

           loader.desactive();
       },
    });
}

// Get Publications

var publicationsOffset = 0;

function chargeMorePublications() {
    publicationsOffset += 10;

    getPublications(publicationsOffset, 10);
}

function getPublications(offset, limit) {
    loader.active();
    $.ajax({
        url: "php/publicationSystem.php",
        method: "post",
        data: `type=get&offset=${offset}&limit=${limit}`,
        success: data => {
            data = JSON.parse(data);

            if(data.publications.length < 10) {
                $("#chargeComments").remove();
            }

            appendPublications(data.publications, data.username);
            loader.desactive();
        },
    });
}

function appendPublications(publications, username) {
    publications.forEach(publication => {
        var image = "";
        var heartState = "";
        var buttonDelete = "";
        var likeMsg = `A ${publication.likes} personas les a gustado esta publicacion.`;
        if(publication.imageURL != "") {
            image = `<img src="${publication.imageURL}" class="w-100" alt="imagen de una publicacion" />`;
        }

        if(publication.liked) {
            if(publication.likes == 1) {
                likeMsg = `A ti te gusta esta publicacion.`;
            }else{
                likeMsg = `A ti y a ${publication.likes-1} personas mas le a gustado esta publicacion.`;
            }
            heartState = "red";
        }

        if(publication.userPublication) {
            buttonDelete = `
            <button class="float-right more-options" title="Eliminar la publicacion" onclick="deletePublication(${publication.id})">
                <span class="fa fa-trash"></span>
            </button>`;
        }
        $("#all-publications-container").append(`<div id="publication-${publication.id}">
            <div class="col-12 mt-5">
                <div class="profile-container">
                    <a class="h4" href="User/${publication.username}">${publication.username}</a>
                    <span>${publication.date}</span>
                </div>

                ${image}

                <div class="content-container">
                    ${publication.content}
                </div>

                <div class="like-container">
                    <button class="${heartState}" id="heart-${publication.id}" onclick="like(${publication.id})">
                        <span class="fa fa-heart"></span>
                    </button>

                    <span class="info" id="info-${publication.id}">${likeMsg}</span>

                    ${buttonDelete}
                </div>
            </div>

            <div class="col-12 pl-5 mt-2">
                <form action="#" method="post" class="add-comment-form">
                    <input type="hidden" name="publicationId" value="${publication.id}" />
                    <textarea class="w-100" name="content" required></textarea>
                    <input type="hidden" name="username" value="${username}" />
                    <button class="btn btn-primary w-100" type="button" onclick="addComment(this)">
                        Comentar
                    </button>
                </form>
            </div>
            <div id="comments-container-${publication.id}">
        `);
        publication.comments.forEach(comment => {
            var deleteButton = "";

            if(comment.userComment) {
                deleteButton = `<button class="btn btn-link p-0 delete" onclick="deleteComment(${comment.id}, ${publication.id})">
                                    Eliminar
                                </button>`;
            }

            $("#all-publications-container").append(`
                <div class="col-12 pl-5 mt-3" id="comment-${publication.id}-${comment.id}">
                    <div class="comment-profile-container">
                        <a class="h6" href="User/${comment.username}">${comment.username}</a>
                        <span class="date">${comment.date}</span>
                        <span class="content">${comment.content}</span>

                        ${deleteButton}

                    </div>
                </div>
            `);
        });
        var moreComments = "";

        if(publication.comments.length >= 10) {
            moreComments = `
            <div class="col-12 pl-5 text-center" id="charge-comments-button-${publication.id}" onclick="chargeMoreComments(${publication.id})">
                <button class="btn btn-link">
                    Cargar mas comentarios.
                </button>
            </div>`;
        }

        $("#all-publications-container").append(`</div>${moreComments}</div>`);
    });
}