# Recreation API with symfony 5 and PHP8
### Requirements
1. docker with docker-composer
2. or PHP8 with composer installed.

### Installation
with docker
1. docker-compose up --build
1. connect to php container with docker exec -it phptask bash
1. execute migrations inside container with php bin/console doctrine:migrations:migrate
1. load fixtures with php bin/console doctrine:fixtures:load
1. play with api, with postman or other tool, it's available on http://localhost:5555

with composer
1. composer install
1. execute migrations with php bin/console doctrine:migrations:migrate
1. load fixtures with php bin/console doctrine:fixtures:load
1. run server php -S localhost:8000 -t public   
1. play with api with postman or other tool its available on http://localhost:5555

### Endpoints
1. GET.
   1. /posts =>returns all posts with pagination metadata.    
   1. /posts/{id}=> return post with id info.    
   1. /posts/{id}/comments => return post with id comments.    
   1. /comments => return all comments with pagination metadata.    
   1. /comments/{id} => return comment with given id.
2. POST.    
   1. /register => required JSON body {"username":"username","password":"password","password2":"password2"} allows create user account
   1. /login => required JSON in body {"username":"username","password":"password"}
   after performing migration 1 admin user is available {"username":"admin","password":"1234"}
   and 1 regular user {"username":"random","password":"1234"}
   it returns Json web token with 100min expiration.    
   1. /posts => required body {"fullContent":"Content","shortContent":"Content"} and JWT belonging to admin user in auth header as Bearer Token, 
   creates new post and returns success message or error message.    
   1. /posts/{id} => required token of admin user, and body {"postId":id,"fullContent":"Content","shortContent":"Content"} updates post with given id 
      and returns success message or error message.
   1. /comments => required token of any user, required body {"postId":id,"comment":"Content"}, create comment.   
3. PUT.
   1. /posts/{id} =>required token of admin user allows edit post.
   1. /comments/{id} => required token of given comment author, allows edit if request comes from author/
4. PATCH
   1. /posts/{id} => required admin token, fullContent or shortContent or both allows edit post.
   1. /comments/{id} => required admin token, and json body {"publish":true} to publish comment.
5. DELETE
   1. /comments/{id} => required admin or comment author token to remove comment.
   1. /posts/{id} => required admin token removes posts