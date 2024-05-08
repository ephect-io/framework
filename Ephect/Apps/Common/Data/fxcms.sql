PRAGMA synchronous = OFF;
PRAGMA journal_mode = MEMORY;
BEGIN TRANSACTION;
CREATE TABLE "__app_document" (
  "app_id" INTEGER NOT NULL,
  "doc_id" INTEGER NOT NULL,
  PRIMARY KEY ("app_id","doc_id")
  CONSTRAINT "FK_app_document_applications" FOREIGN KEY ("app_id") REFERENCES "applications" ("app_id"),
  CONSTRAINT "FK_app_document_documents" FOREIGN KEY ("doc_id") REFERENCES "documents" ("doc_id")
);
CREATE TABLE "__dbconn_app" (
  "dbc_id" INTEGER NOT NULL,
  "app_id" INTEGER NOT NULL,
  PRIMARY KEY ("dbc_id","app_id")
  CONSTRAINT "FK_dbconn_app_applications" FOREIGN KEY ("app_id") REFERENCES "applications" ("app_id"),
  CONSTRAINT "FK_dbconn_app_dbconn" FOREIGN KEY ("dbc_id") REFERENCES "dbconn" ("dbc_id")
);
CREATE TABLE "__document_page" (
  "doc_id" INTEGER NOT NULL,
  "pa_id" INTEGER NOT NULL,
  PRIMARY KEY ("doc_id","pa_id")
  CONSTRAINT "FK_document_page_documents" FOREIGN KEY ("doc_id") REFERENCES "documents" ("doc_id"),
  CONSTRAINT "FK_document_page_pages" FOREIGN KEY ("pa_id") REFERENCES "pages" ("pa_id")
);
CREATE TABLE "__page_block" (
  "pa_id" INTEGER NOT NULL,
  "bl_id" INTEGER NOT NULL,
  PRIMARY KEY ("pa_id","bl_id")
  CONSTRAINT "FK_page_block_blocks" FOREIGN KEY ("bl_id") REFERENCES "blocks" ("bl_id"),
  CONSTRAINT "FK_page_block_pages" FOREIGN KEY ("pa_id") REFERENCES "pages" ("pa_id")
);
CREATE TABLE "__member_newletter" (
  "mbr_id" INTEGER NOT NULL,
  "nl_id" INTEGER NOT NULL,
  PRIMARY KEY ("mbr_id","nl_id")
  CONSTRAINT "FK_member_newletter_members" FOREIGN KEY ("mbr_id") REFERENCES "members" ("mbr_id"),
  CONSTRAINT "FK_member_newletter_newsletter" FOREIGN KEY ("nl_id") REFERENCES "newsletter" ("nl_id")
);
CREATE TABLE "__user_app" (
  "usr_id" INTEGER NOT NULL,
  "app_id" INTEGER NOT NULL,
  PRIMARY KEY ("usr_id","app_id")
  CONSTRAINT "FK_user_app_applications" FOREIGN KEY ("app_id") REFERENCES "applications" ("app_id"),
  CONSTRAINT "FK_user_app_users" FOREIGN KEY ("usr_id") REFERENCES "users" ("usr_id")
);
CREATE TABLE "_block_type" (
  "bt_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "bt_type" TEXT NOT NULL
);
INSERT INTO "_block_type" VALUES (1,'page      ');
INSERT INTO "_block_type" VALUES (2,'menu      ');
CREATE TABLE "_bug_status" (
  "bs_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "bs_status" TEXT
);
INSERT INTO "_bug_status" VALUES (1,'à fixer');
INSERT INTO "_bug_status" VALUES (2,'en cours');
INSERT INTO "_bug_status" VALUES (3,'fixé');
INSERT INTO "_bug_status" VALUES (4,'suspendu');
INSERT INTO "_bug_status" VALUES (5,'abandonné');
CREATE TABLE "_dbserver_type" (
  "dbs_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "dbs_type" TEXT NOT NULL
);
INSERT INTO "_dbserver_type" VALUES (1,'MySQL');
INSERT INTO "_dbserver_type" VALUES (2,'SQL Server');
INSERT INTO "_dbserver_type" VALUES (3,'SQLite');
CREATE TABLE "_document_type" (
  "dt_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "dt_type" TEXT NOT NULL
);
CREATE TABLE "_page_type" (
  "ft_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "ft_type" TEXT NOT NULL
);
INSERT INTO "_page_type" VALUES (1,'html');
INSERT INTO "_page_type" VALUES (2,'php');
INSERT INTO "_page_type" VALUES (3,'aspx');
CREATE TABLE "_protocol_type" (
  "prt_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "prt_type" TEXT
);
CREATE TABLE "applications" (
  "app_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "app_name" TEXT,
  "di_id" INTEGER,
  "sto_id" INTEGER,
  CONSTRAINT "FK_applications_dictionary" FOREIGN KEY ("di_id") REFERENCES "dictionary" ("di_id"),
  CONSTRAINT "FK_applications_storage" FOREIGN KEY ("sto_id") REFERENCES "storage" ("sto_id")
);
INSERT INTO "applications" VALUES (1,NULL,19,NULL);
CREATE TABLE "blocks" (
  "bl_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "bl_column" TEXT,
  "bt_id" INTEGER NOT NULL,
  "di_id" INTEGER NOT NULL,
  CONSTRAINT "FK_blocks_block_type" FOREIGN KEY ("bt_id") REFERENCES "_block_type" ("bt_id"),
  CONSTRAINT "FK_blocks_dictionary" FOREIGN KEY ("di_id") REFERENCES "dictionary" ("di_id")
);
INSERT INTO "blocks" VALUES (1,'1',2,0);
CREATE TABLE "bugreport" (
  "br_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "br_title" TEXT,
  "br_text" TEXT,
  "br_importance" INTEGER,
  "br_date" NUMERIC,
  "br_time" NUMERIC,
  "bs_id" INTEGER,
  "usr_id" INTEGER,
  "app_id" INTEGER,
  CONSTRAINT "FK_bugreport_applications" FOREIGN KEY ("app_id") REFERENCES "applications" ("app_id"),
  CONSTRAINT "FK_bugreport_bug_status" FOREIGN KEY ("bs_id") REFERENCES "_bug_status" ("bs_id"),
  CONSTRAINT "FK_bugreport_users" FOREIGN KEY ("usr_id") REFERENCES "users" ("usr_id")
);
CREATE TABLE "changelog" (
  "cl_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "cl_title" TEXT,
  "cl_text" TEXT,
  "cl_date" NUMERIC,
  "cl_time" NUMERIC,
  "app_id" INTEGER,
  "usr_id" INTEGER,
  CONSTRAINT "FK_changelog_applications" FOREIGN KEY ("app_id") REFERENCES "applications" ("app_id"),
  CONSTRAINT "FK_changelog_users" FOREIGN KEY ("usr_id") REFERENCES "users" ("usr_id")
);
CREATE TABLE "dbconn" (
  "dbc_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "dbc_host" TEXT NOT NULL,
  "dbc_database" TEXT NOT NULL,
  "dbc_login" TEXT NOT NULL,
  "dbc_passwd" TEXT NOT NULL,
  "dbs_id" INTEGER NOT NULL,
  CONSTRAINT "FK_dbconn_dbserver_type" FOREIGN KEY ("dbs_id") REFERENCES "_dbserver_type" ("dbs_id")
);
INSERT INTO "dbconn" VALUES (1,'localhost','ladmin','root','1p2+ar',1);
CREATE TABLE "dictionary" (
  "di_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "di_name" TEXT,
  "di_fr_short" TEXT,
  "di_fr_long" TEXT,
  "di_en_short" TEXT,
  "di_en_long" TEXT,
  "di_ru_short" TEXT,
  "di_ru_long" TEXT
);
INSERT INTO "dictionary" VALUES (0,'na','N/A','N/A','N/A','N/A','','');
INSERT INTO "dictionary" VALUES (1,'applicat','Applications','Liste des applications','Applications','List of applications','','');
INSERT INTO "dictionary" VALUES (2,'blocks','Blocs','Liste des blocs','Blocks','List of blocks','','');
INSERT INTO "dictionary" VALUES (3,'bugrepor','Bugs','Rapport de bugs','Bugs','Bug reports','','');
INSERT INTO "dictionary" VALUES (4,'changelo','Changements','Notes de changements','Changes','Change log','','');
INSERT INTO "dictionary" VALUES (5,'dictiona','Dictionnaire','','Dictionary','','','');
INSERT INTO "dictionary" VALUES (6,'editor','Editer','Editer les attributs du script','Edit','Edit script attributes','','');
INSERT INTO "dictionary" VALUES (7,'forums','Forums','Forums disponibles','Forums','Available forums','','');
INSERT INTO "dictionary" VALUES (8,'groups','Groupes','Liste des groupes','Groups','List of groups','','');
INSERT INTO "dictionary" VALUES (9,'mkmain','Accueil','Accueil','Home','Home page','','');
INSERT INTO "dictionary" VALUES (10,'members','Accès membres','Gérez votre profil membre','Members area','Manage your Data','','');
INSERT INTO "dictionary" VALUES (11,'menus','Menus','Entrées de menus','Menus','Menu items','','');
INSERT INTO "dictionary" VALUES (12,'mkblock','Créer un bloc','Créer un nouveau bloc','Create a block','Create a new block','','');
INSERT INTO "dictionary" VALUES (13,'mkfields','Champs','Champs de la table','Fields','Table fields','','');
INSERT INTO "dictionary" VALUES (14,'mkfile','Fichier','Création du fichier','File','Creation of the file','','');
INSERT INTO "dictionary" VALUES (15,'mkmenu','Créer un menu','Créer une nouvelle entrée de menu','Create a menu','Create a new menu item','','');
INSERT INTO "dictionary" VALUES (16,'mkscript','Créer un script','Créer un script à partir d''une table','Create a script','Create a script from a table','','');
INSERT INTO "dictionary" VALUES (17,'pages','Pages','Liste des pages','Pages','List of pages','','');
INSERT INTO "dictionary" VALUES (18,'todo','A faire','Liste des tâches','To do','Tasks to do','','');
INSERT INTO "dictionary" VALUES (19,'webfacto','WebFactory','WebFactory','WebFactory','WebFactory','','');
CREATE TABLE "documents" (
  "doc_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "doc_name" TEXT NOT NULL,
  "doc_title" TEXT NOT NULL,
  "doc_content" TEXT,
  "doc_dir" TEXT NOT NULL,
  "doc_url" TEXT NOT NULL,
  "dt_id" INTEGER NOT NULL,
  CONSTRAINT "FK_documents_document_type" FOREIGN KEY ("dt_id") REFERENCES "_document_type" ("dt_id")
);
CREATE TABLE "pages" (
  "pa_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "pa_filename" TEXT,
  "pa_directory" TEXT,
  "pa_url" TEXT,
  "di_id" INTEGER,
  "ft_id" INTEGER,
  "app_id" INTEGER, 
  CONSTRAINT "FK_pages_applications" FOREIGN KEY ("app_id") REFERENCES "applications" ("app_id"),
  CONSTRAINT "FK_pages_dictionary" FOREIGN KEY ("di_id") REFERENCES "dictionary" ("di_id"),
  CONSTRAINT "FK_pages_page_type" FOREIGN KEY ("ft_id") REFERENCES "_page_type" ("ft_id")
);
INSERT INTO "pages" VALUES (17,'mkmain.php','.','',9,2,1);
INSERT INTO "pages" VALUES (18,'menus.php','.','',11,2,1);
INSERT INTO "pages" VALUES (19,'pages.php','.','',17,2,1);
INSERT INTO "pages" VALUES (20,'blocks.php','.','',2,2,1);
INSERT INTO "pages" VALUES (21,'dictionary.php','.','',5,2,1);
INSERT INTO "pages" VALUES (22,'applications.php','.','',1,2,1);
INSERT INTO "pages" VALUES (23,'forums.php','.','',7,2,1);
INSERT INTO "pages" VALUES (24,'changelog.php','.','',4,2,1);
INSERT INTO "pages" VALUES (25,'todo.php','.','',18,2,1);
INSERT INTO "pages" VALUES (26,'bugreport.php','.','',3,2,1);
INSERT INTO "pages" VALUES (27,'groups.php','.','',8,2,1);
INSERT INTO "pages" VALUES (28,'newsletter.php','.','',0,2,1);
INSERT INTO "pages" VALUES (29,'mkscript.php','.','',16,2,1);
INSERT INTO "pages" VALUES (30,'mkmenu.php','.','',15,2,1);
INSERT INTO "pages" VALUES (31,'mkblock.php','.','',12,2,1);
INSERT INTO "pages" VALUES (32,'mkfields.php','.','',13,2,1);
INSERT INTO "pages" VALUES (33,'mkfile.php','.','',14,2,1);
CREATE TABLE "groups" (
  "grp_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "grp_name" TEXT NOT NULL,
  "grp_members_priv" TEXT NOT NULL,
  "grp_menu_priv" TEXT NOT NULL,
  "grp_page_priv" TEXT NOT NULL,
  "grp_news_priv" TEXT NOT NULL,
  "grp_items_priv" TEXT NOT NULL,
  "grp_database_priv" TEXT NOT NULL,
  "grp_images_priv" TEXT NOT NULL,
  "grp_calendar_priv" TEXT NOT NULL,
  "grp_newsletter_priv" TEXT NOT NULL,
  "grp_forum_priv" TEXT NOT NULL,
  "grp_users_priv" TEXT NOT NULL
);
INSERT INTO "groups" VALUES (1,'root           ','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');
CREATE TABLE "members" (
  "mbr_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "mbr_name" TEXT,
  "mbr_adr1" TEXT,
  "mbr_adr2" TEXT,
  "mbr_cp" TEXT,
  "mbr_email" TEXT,
  "mbr_login" TEXT,
  "mbr_password" TEXT
);
INSERT INTO "members" VALUES (1,'Lambda','Pas d''adresse','','76000','dblanchard1@bbox.fr','lambda','1MF14m2p97');
CREATE TABLE "menus" (
  "me_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "me_level" TEXT,
  "me_target" TEXT,
  "pa_id" INTEGER,
  "bl_id" INTEGER,
  CONSTRAINT "FK_menus_blocks" FOREIGN KEY ("bl_id") REFERENCES "blocks" ("bl_id"),
  CONSTRAINT "FK_menus_pages" FOREIGN KEY ("pa_id") REFERENCES "pages" ("pa_id")
);
INSERT INTO "menus" VALUES (17,'1','page',17,1);
INSERT INTO "menus" VALUES (18,'1','page',18,1);
INSERT INTO "menus" VALUES (19,'1','page',19,1);
INSERT INTO "menus" VALUES (20,'1','page',20,1);
INSERT INTO "menus" VALUES (21,'1','page',21,1);
INSERT INTO "menus" VALUES (22,'2','page',22,1);
INSERT INTO "menus" VALUES (23,'0','page',23,1);
INSERT INTO "menus" VALUES (24,'1','page',24,1);
INSERT INTO "menus" VALUES (25,'1','page',25,1);
INSERT INTO "menus" VALUES (26,'1','page',26,1);
INSERT INTO "menus" VALUES (27,'1','page',27,1);
INSERT INTO "menus" VALUES (29,'0','page',29,1);
INSERT INTO "menus" VALUES (30,'0','page',30,1);
INSERT INTO "menus" VALUES (31,'0','page',31,1);
INSERT INTO "menus" VALUES (32,'0','page',32,1);
INSERT INTO "menus" VALUES (33,'0','page',33,1);
CREATE TABLE "newsletter" (
  "nl_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "nl_title" TEXT,
  "nl_author" TEXT,
  "nl_header" TEXT,
  "nl_image" TEXT,
  "nl_comment" TEXT,
  "nl_body" TEXT,
  "nl_links" TEXT,
  "nl_footer" TEXT,
  "nl_file" TEXT,
  "nl_date" NUMERIC
);
CREATE TABLE "queries" (
  "qy_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "qy_name" TEXT NOT NULL,
  "qy_text" TEXT NOT NULL,
  "dbc_id" INTEGER NOT NULL,
  CONSTRAINT "FK_queries_dbconn" FOREIGN KEY ("dbc_id") REFERENCES "dbconn" ("dbc_id")
);
CREATE TABLE "storage" (
  "sto_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "sto_root_dir" TEXT NOT NULL,
  "sto_host" TEXT NOT NULL,
  "sto_port" INTEGER,
  "usr_id" INTEGER,
  "prt_id" INTEGER,
  CONSTRAINT "FK_storage_protocol_type" FOREIGN KEY ("prt_id") REFERENCES "_protocol_type" ("prt_id"),
  CONSTRAINT "FK_storage_users" FOREIGN KEY ("usr_id") REFERENCES "users" ("usr_id")
);
INSERT INTO "storage" VALUES (1,'admin','http://localhost',NULL,NULL,NULL);
CREATE TABLE "todo" (
  "td_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "td_title" TEXT,
  "td_text" TEXT,
  "td_priority" INTEGER,
  "td_expiry" NUMERIC,
  "td_status" TEXT,
  "td_date" NUMERIC,
  "td_time" NUMERIC,
  "app_id" INTEGER,
  "usr_id" INTEGER,
  "usr_id2" INTEGER,
  CONSTRAINT "FK_todo_applications" FOREIGN KEY ("app_id") REFERENCES "applications" ("app_id"),
  CONSTRAINT "FK_todo_users" FOREIGN KEY ("usr_id") REFERENCES "users" ("usr_id"),
  CONSTRAINT "FK_todo_users1" FOREIGN KEY ("usr_id2") REFERENCES "users" ("usr_id")
);
CREATE TABLE "users" (
  "usr_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "mbr_id" INTEGER NOT NULL,
  "grp_id" INTEGER NOT NULL,
  CONSTRAINT "FK_users_groups" FOREIGN KEY ("grp_id") REFERENCES "groups" ("grp_id"),
  CONSTRAINT "FK_users_members" FOREIGN KEY ("mbr_id") REFERENCES "members" ("mbr_id")
);
INSERT INTO "users" VALUES (1,1,1);
INSERT INTO "users" VALUES (2,2,1);
CREATE INDEX "dictionary_PK_dictionary" ON "dictionary" ("di_id");
CREATE INDEX "dbconn_PK_dbconn" ON "dbconn" ("dbc_id");
CREATE INDEX "dbconn_FK_dbconn_dbserver_type" ON "dbconn" ("dbs_id");
CREATE INDEX "_document_type_PK_document_type" ON "_document_type" ("dt_id");
CREATE INDEX "_block_type_PK_block_type" ON "_block_type" ("bt_id");
CREATE INDEX "blocks_PK_blocks" ON "blocks" ("bl_id");
CREATE INDEX "blocks_FK_blocks_block_type" ON "blocks" ("bt_id");
CREATE INDEX "blocks_FK_blocks_dictionary" ON "blocks" ("di_id");
CREATE INDEX "_dbserver_type_PK_dbserver_type" ON "_dbserver_type" ("dbs_id");
CREATE INDEX "groups_PK_groups" ON "groups" ("grp_id");
CREATE INDEX "__dbconn_app_PK_dbconn_app" ON "__dbconn_app" ("dbc_id","app_id");
CREATE INDEX "__dbconn_app_FK_dbconn_app_applications" ON "__dbconn_app" ("app_id");
CREATE INDEX "__page_block_PK_page_block" ON "__page_block" ("pa_id","bl_id");
CREATE INDEX "__page_block_FK_page_block_blocks" ON "__page_block" ("bl_id");
CREATE INDEX "newsletter_PK_newsletter" ON "newsletter" ("nl_id");
CREATE INDEX "members_PK_members" ON "members" ("mbr_id");
CREATE INDEX "_bug_status_PK_bug_status" ON "_bug_status" ("bs_id");
CREATE INDEX "bugreport_PK_bugreport" ON "bugreport" ("br_id");
CREATE INDEX "bugreport_FK_bugreport_bug_status" ON "bugreport" ("bs_id");
CREATE INDEX "bugreport_FK_bugreport_users" ON "bugreport" ("usr_id");
CREATE INDEX "bugreport_FK_bugreport_applications" ON "bugreport" ("app_id");
CREATE INDEX "applications_PK_applications" ON "applications" ("app_id");
CREATE INDEX "applications_FK_applications_dictionary" ON "applications" ("di_id");
CREATE INDEX "applications_FK_applications_storage" ON "applications" ("sto_id");
CREATE INDEX "changelog_PK_changelog" ON "changelog" ("cl_id");
CREATE INDEX "changelog_FK_changelog_users" ON "changelog" ("usr_id");
CREATE INDEX "changelog_FK_changelog_applications" ON "changelog" ("app_id");
CREATE INDEX "_protocol_type_PK_protocol_type" ON "_protocol_type" ("prt_id");
CREATE INDEX "todo_PK_todo" ON "todo" ("td_id");
CREATE INDEX "todo_FK_todo_users" ON "todo" ("usr_id");
CREATE INDEX "todo_FK_todo_users1" ON "todo" ("usr_id2");
CREATE INDEX "todo_FK_todo_applications" ON "todo" ("app_id");
CREATE INDEX "menus_PK_menus" ON "menus" ("me_id");
CREATE INDEX "menus_FK_menus_blocks" ON "menus" ("bl_id");
CREATE INDEX "menus_FK_menus_pages" ON "menus" ("pa_id");
CREATE INDEX "pages_PK_pages" ON "pages" ("pa_id");
CREATE INDEX "pages_FK_pages_page_type" ON "pages" ("ft_id");
CREATE INDEX "pages_FK_pages_applications" ON "pages" ("app_id");
CREATE INDEX "pages_FK_pages_dictionary" ON "pages" ("di_id");
CREATE INDEX "users_PK_users" ON "users" ("usr_id");
CREATE INDEX "users_FK_users_groups" ON "users" ("grp_id");
CREATE INDEX "users_FK_users_members" ON "users" ("mbr_id");
CREATE INDEX "__user_app_PK_user_app" ON "__user_app" ("usr_id","app_id");
CREATE INDEX "__user_app_FK_user_app_applications" ON "__user_app" ("app_id");
CREATE INDEX "storage_PK_storage" ON "storage" ("sto_id");
CREATE INDEX "storage_FK_storage_protocol_type" ON "storage" ("prt_id");
CREATE INDEX "storage_FK_storage_users" ON "storage" ("usr_id");
CREATE INDEX "_page_type_PK_page_type" ON "_page_type" ("ft_id");
CREATE INDEX "__document_page_PK_document_page" ON "__document_page" ("doc_id","pa_id");
CREATE INDEX "__document_page_FK_document_page_pages" ON "__document_page" ("pa_id");
CREATE INDEX "documents_PK_documents" ON "documents" ("doc_id");
CREATE INDEX "documents_FK_documents_document_type" ON "documents" ("dt_id");
CREATE INDEX "__member_newletter_PK_member_newletter" ON "__member_newletter" ("mbr_id","nl_id");
CREATE INDEX "__member_newletter_FK_member_newletter_newsletter" ON "__member_newletter" ("nl_id");
CREATE INDEX "__app_document_PK_document_app" ON "__app_document" ("app_id","doc_id");
CREATE INDEX "__app_document_FK_app_document_documents" ON "__app_document" ("doc_id");
CREATE INDEX "queries_PK_queries" ON "queries" ("qy_id");
CREATE INDEX "queries_FK_queries_dbconn" ON "queries" ("dbc_id");
END TRANSACTION;
