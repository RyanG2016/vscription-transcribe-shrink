create table if not exists act_log
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
;
create table if not exists cities
(
    id      int auto_increment
        primary key,
    country int         not null,
    city    varchar(50) not null
)
    ;

create table if not exists countries
(
    id      int auto_increment
        primary key,
    country varchar(50) not null
)
    ;

create table if not exists downloads
(
    id      int auto_increment
        primary key,
    acc_id  int           not null,
    hash    varchar(40)   not null,
    file_id int           not null,
    expired int default 0 not null
)
    ;

create table if not exists file_speaker_type
(
    id   int auto_increment
        primary key,
    name varchar(100) not null
);

create table if not exists accounts
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
    sr_enabled             tinyint        default 0                   not null,
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
    ;

create table if not exists file_status_ref
(
    id            int auto_increment
        primary key,
    j_status_id   int         not null,
    j_status_name varchar(25) not null
)
    ;

create table if not exists files
(
    file_id                    int auto_increment,
    job_id                     varchar(10)                            not null,
    acc_id                     int        default 1                   not null,
    file_type                  int                                    null,
    org_ext                    varchar(20)                            null,
    filename                   varchar(254)                           null,
    tmp_name                   varchar(50)                            null,
    orig_filename              varchar(254)                           null,
    job_document_html          longtext                               null,
    job_document_rtf           longtext                               null,
    has_caption                tinyint    default 0                   not null,
    captions                   mediumtext                             null,
    file_tag                   varchar(254)                           null,
    file_author                varchar(254)                           null,
    file_work_type             varchar(254)                           null,
    file_comment               varchar(254)                           null,
    file_speaker_type          int        default 0                   not null,
    file_date_dict             datetime                               null,
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
    user_field_1               varchar(254)                           null,
    user_field_2               varchar(254)                           null,
    user_field_3               varchar(254)                           null,
    deleted                    tinyint(1) default 0                   not null,
    constraint `key`
        unique (file_id),
    constraint files_accounts_acc_id_fk
        foreign key (acc_id) references accounts (acc_id)
);

create table if not exists conversion
(
    id      int auto_increment
        primary key,
    file_id int           not null,
    status  int default 0 not null comment '0: pending, 1: done, 2: need manual review, 3: failed',
    constraint conversion_file_id_uindex
        unique (file_id),
    constraint conversion_files_file_id_fk
        foreign key (file_id) references files (file_id)
);

create table if not exists protect
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
    ;

create table if not exists roles
(
    role_id   int auto_increment
        primary key,
    role_name varchar(23)  not null,
    role_desc varchar(255) null
)
    ;

create table if not exists access
(
    access_id int auto_increment
        primary key,
    acc_id    int          not null,
    uid       int          not null,
    username  varchar(255) null,
    acc_role  int          not null,
    constraint access_accounts_acc_id_fk
        foreign key (acc_id) references accounts (acc_id),
    constraint access_roles_role_id_fk
        foreign key (acc_role) references roles (role_id)
)
    ;

create table if not exists speech_recognition
(
    sr_id                int auto_increment
        primary key,
    account_id           int                         null,
    sr_rate              decimal(10, 2) default 0.00 null,
    sr_flat_rate         decimal(10, 2) default 0.00 null,
    sr_vocab             mediumtext     default ''   null,
    sr_minutes_remaining decimal(10, 2) default 0.00 null,
    constraint speech_recognition_accounts_acc_id_fk
        unique (account_id),
    constraint speech_recognition_accounts_acc_id_fk
        foreign key (account_id) references accounts (acc_id)
);

create table if not exists sr_packages
(
    srp_id      int auto_increment
        primary key,
    srp_name    varchar(250) default '' null,
    srp_minutes decimal(10, 2)          not null,
    srp_price   decimal(10, 2)          not null,
    srp_desc    tinytext     default '' null
);

create table if not exists srq_status_ref
(
    srq_status_ref_id int auto_increment
        primary key,
    srq_status        int         not null,
    srq_status_desc   varchar(30) not null,
    constraint srq_status_ref_pk
        unique (srq_status)
);

create table if not exists sr_queue
(
    srq_id            int auto_increment
        primary key,
    file_id           int                         null,
    srq_status        int                         null,
    srq_tmp_filename  varchar(70)                 null,
    srq_revai_id      tinytext                    null,
    srq_revai_minutes decimal(10, 2) default 0.00 null,
    notes             varchar(100)                null,
    srq_internal_id   int                         null comment 'internal processing id for files OK from rev.ai',
    refunded          tinyint        default 0    not null,
    constraint sr_queue_srq_internal_id_uindex
        unique (srq_internal_id),
    constraint sr_queue_files_file_id_fk
        foreign key (file_id) references files (file_id),
    constraint sr_queue_srq_status_ref_srq_status_fk
        foreign key (srq_status) references srq_status_ref (srq_status)
);

create table if not exists sr_log
(
    srlog_id        int auto_increment
        primary key,
    srq_id          int                                   null,
    file_id         int                                   null,
    srlog_activity  varchar(200)                          null,
    srqlog_msg      mediumtext                            null,
    srlog_timestamp timestamp default current_timestamp() not null,
    constraint sr_log_sr_queue_srq_id_fk
        foreign key (srq_id) references sr_queue (srq_id)
);

create table if not exists tokens
(
    id         int auto_increment
        primary key,
    email      varchar(100)                          not null,
    identifier text                                  not null,
    time       timestamp default current_timestamp() not null,
    used       int       default 0                   not null,
    token_type int       default 4                   not null comment '4:pwd reset, 5:verify account, 7: verify account + accept typist invite with accID in ext1',
    extra1     int       default 0                   null,
    extra2     int       default 0                   null
)
    ;

create table if not exists typist_log
(
    tlog_id           int auto_increment
        primary key,
    uid               int       not null comment 'typist user id',
    job_id            int       not null comment 'job working on',
    job_start_date    timestamp null,
    job_complete_date timestamp null,
    job_length        int       not null comment 'audio file length in sec'
)
    ;

create table if not exists userlog
(
    id       int auto_increment
        primary key,
    email    varchar(255)                          not null,
    user_ip  varbinary(16)                         not null,
    action   varchar(150)                          not null,
    log_time timestamp default current_timestamp() not null
)
    ;

create table if not exists users
(
    id                 int auto_increment
        primary key,
    first_name         varchar(50)                              not null,
    last_name          varchar(50)                              not null,
    email              varchar(200)                             not null,
    password           varchar(61)                              not null,
    country_id         int          default 0                   not null,
    city               varchar(100)                             null,
    country            varchar(100)                             null,
    zipcode            varchar(20)                              null,
    state_id           int                                      null,
    state              varchar(100)                             null,
    address            varchar(101) default ''                  not null,
    registeration_date timestamp    default current_timestamp() not null,
    last_ip_address    varchar(17)                              null,
    typist             int          default 0                   not null comment '0: not a typist, 1: available for work, 2: temporarily off for work',
    account_status     int                                      not null comment '0: locked temporarily,
1: active,
5: pending email verification',
    last_login         timestamp    default current_timestamp() not null,
    trials             int          default 0                   not null,
    unlock_time        timestamp                                null,
    newsletter         int                                      not null,
    def_access_id      int                                      null,
    shortcuts          text         default 0                   not null,
    dictionary         text         default 0                   not null,
    email_notification tinyint(1)   default 1                   not null,
    enabled            tinyint(1)                               not null comment 'disables the account completely if set to 0',
    account            int          default 0                   not null,
    tutorials          text         default '{}'                not null,
    constraint email
        unique (email),
    constraint users_access_access_id_fk
        foreign key (def_access_id) references access (access_id)
)
    ;

alter table access
    add constraint access_users_id_fk
        foreign key (uid) references users (id);

create table if not exists payments
(
    payment_id   int auto_increment
        primary key,
    user_id      int                                   null,
    pkg_id       int                                   null,
    amount       decimal(10, 2)                        null comment 'in cad',
    ref_id       varchar(20)                           null,
    trans_id     text                                  null,
    payment_json longtext collate utf8mb4_bin          null,
    status       int                                   null,
    timestamp    timestamp default current_timestamp() not null,
    constraint payments_sr_packages_srp_id_fk
        foreign key (pkg_id) references sr_packages (srp_id),
    constraint payments_users_id_fk
        foreign key (user_id) references users (id)
);