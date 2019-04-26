function getFollowers() {
    $.ajax({
        url: "php/followersSystem.php",
        method: "post",
        data: `type=getFollowers&userId=${userId}&limit=${followerLimit}&offset=${followersOffsetNumber}`,
        success: data => {
            const followers = JSON.parse(data);

            if(!followers.length) {
                $("#followers-container").html("<h3 class='text-center mt-4'>No tiene seguidores.</h3>");
            }

            followers.forEach(follower => {
                appendFollower(follower, "followers-container");
            });

            if(followers.length == followerLimit) {
                $("#followers-offset").removeClass("d-none");
            }else{
                $("#followers-offset").addClass("d-none");
            }
        },
    });
}

function followersOffset() {
    followersOffsetNumber += followerLimit;
    getFollowers();
}

function getFollowing() {
    $.ajax({
        url: "php/followersSystem.php",
        method: "post",
        data: `type=getFollowing&userId=${userId}&limit=${followerLimit}&offset=${followersOffsetNumber}`,
        success: data => {
            const followers = JSON.parse(data);

            if(!followers.length) {
                $("#following-container").html("<h3 class='text-center mt-4'>No esta siguiendo a nadien.</h3>");
            }

            followers.forEach(follower => {
                appendFollower(follower, "following-container");
            });

            if(followers.length == followerLimit) {
                $("#following-offset").removeClass("d-none");
            }else{
                $("#following-offset").addClass("d-none");
            }
        },
    });
}

function followingOffset() {
    followersOffsetNumber += followerLimit;
    getFollowing();
}

function appendFollower(follower, containerId) {
    $("#"+containerId).append(`
        <div class="follower-item col-12 col-md-6 col-lg-4 col-xl-3 pb-3">
            <a href="User/${follower.Username}" class="h4 font-weight-bold">${follower.Username}</a>
        </div>
    `);
}