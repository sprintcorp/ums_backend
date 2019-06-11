# USER MANAGEMENT API SERVICES  DEFINITION 

#### User Group Services 
This services uses the methods `GET` , `POST`, `PATCH`, `PATCH`, `DELETE` in consuming the UMS backend service.  

1 . get users and their respective groups.
```bash
GET  /usersgroups
```
Endpoint to see all users that belongs to group (and which group the belong to).

2 . get all users in a group. 
```bash
GET  /usersgroup/{group_id}
```
Endpoint to see all  users that belong to a group

3 . register user to group  
```bash
POST   /usersgroup
```
Endpoint  to register a user to a group
4 . get all users in a group. 
```bash
POST  /usersgroup
```
Endpoint to change the group a user belongs to. (if wrongly registered in a group).

5 . remove a user from a group  
```bash
DELETE  /usersgroup/{user_id}
```
Endpoint  to delete a user from a group
