alter table `user` add column 'first_name' varchar(64) default null;
alter table `user` add column 'last_name' varchar(64) default null;

/* Modify column size based on how many goups a user might be a member of */
alter table `user` add column 'groups' varchar(512) default null;

/* Modify column size based on how much identity information is returned from IdP */
alter table `user` add column 'raw_identity' varcahr(8192) default null;