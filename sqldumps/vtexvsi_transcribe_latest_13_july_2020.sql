create table act_log
(
    act_log_id   int auto_increment
        primary key,
    username     varchar(100)                          not null,
    act_log_date timestamp default current_timestamp() not null,
    acc_id       int                                   not null,
    actPage      varchar(50)                           not null,
    activity     varchar(255)                          not null,
    ip_addr      varchar(16)                           null
)
    charset = latin1;

create table cities
(
    id      int auto_increment
        primary key,
    country int         not null,
    city    varchar(50) not null
)
    collate = utf8_bin;

create table countries
(
    id      int auto_increment
        primary key,
    country varchar(50) not null
)
    collate = utf8_bin;

create table downloads
(
    id      int auto_increment
        primary key,
    acc_id  int           not null,
    hash    varchar(40)   not null,
    file_id int           not null,
    expired int default 0 not null
)
    charset = latin1;

create table file_speaker_type
(
    id   int auto_increment
        primary key,
    name varchar(100) not null
);

create table accounts
(
    acc_id                 int auto_increment
        primary key,
    enabled                tinyint(1)     default 1                   not null,
    billable               tinyint(1)     default 1                   not null,
    acc_name               varchar(255)                               not null,
    acc_retention_time     int                                        not null,
    acc_creation_date      timestamp      default current_timestamp() not null on update current_timestamp(),
    bill_rate1             decimal(10, 2) default 0.00                not null,
    bill_rate1_type        int                                        not null,
    bill_rate1_TAT         int                                        not null,
    bill_rate1_desc        varchar(255)                               not null,
    bill_rate1_min_pay     decimal(10, 2) default 0.00                not null,
    bill_rate2             decimal(10, 2)                             null,
    bill_rate2_type        int                                        null,
    bill_rate2_TAT         int                                        null,
    bill_rate2_desc        varchar(255)                               null,
    bill_rate2_min_pay     decimal(10, 2)                             null,
    bill_rate3             decimal(10, 2)                             null,
    bill_rate3_type        int                                        null,
    bill_rate3_TAT         int                                        null,
    bill_rate3_desc        varchar(255)                               null,
    bill_rate3_min_pay     decimal(10, 2)                             null,
    bill_rate4             decimal(10, 2)                             null,
    bill_rate4_type        int                                        null,
    bill_rate4_TAT         int                                        null,
    bill_rate4_desc        varchar(255)                               null,
    bill_rate4_min_pay     decimal(10, 2)                             null,
    bill_rate5             decimal(10, 2)                             null,
    bill_rate5_type        int                                        null,
    bill_rate5_TAT         int                                        null,
    bill_rate5_desc        varchar(255)                               null,
    bill_rate5_min_pay     decimal(10, 2)                             null,
    lifetime_minutes       int                                        null,
    work_types             text                                       null,
    next_job_tally         int            default 0                   not null,
    act_log_retention_time int            default 180                 not null,
    job_prefix             varchar(5)     default ''                  not null,
    constraint accounts_file_speaker_type_id_fk
        foreign key (bill_rate1_type) references file_speaker_type (id),
    constraint accounts_file_speaker_type_id_fk_2
        foreign key (bill_rate2_type) references file_speaker_type (id),
    constraint accounts_file_speaker_type_id_fk_3
        foreign key (bill_rate3_type) references file_speaker_type (id),
    constraint accounts_file_speaker_type_id_fk_4
        foreign key (bill_rate4_type) references file_speaker_type (id),
    constraint accounts_file_speaker_type_id_fk_5
        foreign key (bill_rate5_type) references file_speaker_type (id)
)
    collate = utf8_bin;

create table file_status_ref
(
    id            int auto_increment
        primary key,
    j_status_id   int         not null,
    j_status_name varchar(25) not null
)
    charset = latin1;

create table files
(
    file_id                    int auto_increment,
    job_id                     varchar(10)                            not null,
    acc_id                     int        default 1                   not null,
    file_type                  int                                    null,
    original_audio_type        int                                    null,
    filename                   varchar(254)                           null,
    tmp_name                   varchar(50)                            null,
    orig_filename              varchar(254)                           null,
    fileAudioBlob              mediumblob                             null,
    fileTextBlob               mediumblob                             null,
    job_document_html          longtext                               null,
    job_document_rtf           longtext                               null,
    file_tag                   varchar(254)                           null,
    file_author                varchar(254)                           null,
    file_work_type             varchar(254)                           null,
    file_comment               varchar(254)                           null,
    file_speaker_type          int        default 0                   not null,
    file_date_dict             date                                   null,
    file_status                int        default 0                   not null,
    audio_length               int                                    null,
    last_audio_position        int        default 0                   null,
    job_upload_date            timestamp  default current_timestamp() not null,
    job_uploaded_by            varchar(254)                           null,
    text_downloaded_date       timestamp                              null,
    times_text_downloaded_date int        default 0                   not null,
    job_transcribed_by         varchar(254)                           null,
    file_transcribed_date      timestamp                              null,
    elapsed_time               int        default 0                   not null,
    typist_comments            varchar(254)                           null,
    isBillable                 tinyint(1) default 1                   not null,
    billed                     tinyint(1) default 0                   not null,
    typ_billed                 tinyint(1) default 0                   not null,
    deleted                    tinyint(1) default 0                   not null,
    constraint `key`
        unique (file_id),
    constraint files_accounts_acc_id_fk
        foreign key (acc_id) references accounts (acc_id)
)
    charset = utf8mb4;

create table protect
(
    id            int auto_increment
        primary key,
    first_attempt timestamp                             null,
    ip            varchar(16)                           not null,
    last_attempt  timestamp default current_timestamp() not null,
    trials        int                                   not null,
    src           int                                   not null comment '0:reset, 1:login, 2:register',
    locked        int       default 0                   not null,
    unlocks_on    timestamp                             null
)
    collate = utf8_bin;

create table roles
(
    role_id   int auto_increment
        primary key,
    role_name varchar(23)  not null,
    role_desc varchar(255) null
)
    collate = utf8_bin;

create table tokens
(
    id         int auto_increment
        primary key,
    email      varchar(100)                          not null,
    identifier text                                  not null,
    time       timestamp default current_timestamp() not null,
    used       int       default 0                   not null,
    token_type int       default 4                   not null comment '4:pwd reset, 5:verify email'
)
    collate = utf8_bin;

create table typist_log
(
    tlog_id           int auto_increment
        primary key,
    uid               int       not null comment 'typist user id',
    job_id            int       not null comment 'job working on',
    job_start_date    timestamp null,
    job_complete_date timestamp null,
    job_length        int       not null comment 'audio file length in sec'
)
    collate = utf8_bin;

create table userlog
(
    id       int auto_increment
        primary key,
    email    varchar(255)                          not null,
    user_ip  varbinary(16)                         not null,
    action   varchar(150)                          not null,
    log_time timestamp default current_timestamp() not null
)
    collate = utf8_bin;

create table users
(
    id                 int auto_increment
        primary key,
    first_name         varchar(50)                            not null,
    last_name          varchar(50)                            not null,
    email              varchar(200)                           not null,
    password           varchar(61)                            not null,
    country_id         int                                    not null,
    city               varchar(100)                           null,
    state_id           int                                    null,
    state              varchar(100)                           null,
    registeration_date timestamp  default current_timestamp() not null,
    last_ip_address    varchar(17)                            null,
    plan_id            int                                    not null,
    account_status     int                                    not null,
    last_login         timestamp  default current_timestamp() not null,
    trials             int        default 0                   not null,
    unlock_time        timestamp                              null,
    newsletter         int                                    not null,
    shortcuts          text       default 0                   not null,
    dictionary         text       default 0                   not null,
    email_notification tinyint(1) default 1                   not null,
    enabled            tinyint(1)                             not null,
    account            int(1)     default 0                   not null,
    constraint email
        unique (email),
    constraint users_roles_role_id_fk
        foreign key (plan_id) references roles (role_id)
)
    collate = utf8_bin;

create table access
(
    access_id int auto_increment
        primary key,
    acc_id    int          not null,
    uid       int          not null,
    username  varchar(255) not null,
    acc_role  int          not null,
    constraint access_accounts_acc_id_fk
        foreign key (acc_id) references accounts (acc_id),
    constraint access_roles_role_id_fk
        foreign key (acc_role) references roles (role_id),
    constraint access_users_id_fk
        foreign key (uid) references users (id)
)
    collate = utf8_bin;

