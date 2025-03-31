# Useful curl requests for the project

## Create an user

```sh
curl -X POST http://localhost:8000/users \
     -H "Content-Type: application/json" \
     -d '{
           "username": "testuser",
           "email": "test@example.com",
           "password": "securepassword"
         }'
```

## Create a post for the user #1

```sh
curl -X POST http://localhost:8000/posts \
     -H "Content-Type: application/json" \
     -d '{
           "title": "My First Post",
           "content": "This is the content of my first post.",
           "imgurl": "http://example.com/image.jpg",
           "author": 1
         }'
```
