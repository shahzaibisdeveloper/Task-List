create database if not exists todolist;
use todolist;
create table if not exists task(
    id int PRIMARY KEY AUTO_INCREMENT,
    task_name text not null,
    status enum('done', 'ongoing', 'undone')
);