create table if not exists notes(
    id integer generated always as identity,
    name varchar(255) not null,
    path varchar(255) not null
);