create table users
(
    id       serial
        constraint users_pk
            primary key,
    name     varchar not null,
    email    varchar not null,
    password varchar not null
);

alter table users
    owner to test_user;

create table tokens
(
    user_id    integer      not null,
    token      varchar(255) not null,
    expired_at date         not null
);

alter table tokens
    owner to test_user;

create table to_do_lists
(
    id      serial
        constraint to_do_lists_pk
            primary key,
    name    varchar(255) not null,
    user_id integer      not null
);

alter table to_do_lists
    owner to test_user;

create table tasks
(
    id      serial
        constraint tasks_pk
            primary key,
    list_id integer      not null,
    name    varchar(255) not null
);

alter table tasks
    owner to test_user;

