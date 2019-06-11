<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190521093305 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(191) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(191) DEFAULT NULL, UNIQUE INDEX UNIQ_B6A2DD685F37A13B (token), INDEX IDX_B6A2DD6819EB6921 (client_id), INDEX IDX_B6A2DD68A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, application_id INT DEFAULT NULL, random_id VARCHAR(191) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', secret VARCHAR(191) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_C74404553E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(191) DEFAULT NULL, last_name VARCHAR(191) DEFAULT NULL, other_names VARCHAR(191) DEFAULT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(191) NOT NULL, status SMALLINT DEFAULT 1 NOT NULL, password LONGTEXT NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token LONGTEXT DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_8F02BF9DA76ED395 (user_id), INDEX IDX_8F02BF9DFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_application (user_id INT NOT NULL, application_id INT NOT NULL, INDEX IDX_D401454A76ED395 (user_id), INDEX IDX_D4014543E030ACD (application_id), PRIMARY KEY(user_id, application_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(191) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(191) DEFAULT NULL, UNIQUE INDEX UNIQ_C74F21955F37A13B (token), INDEX IDX_C74F219519EB6921 (client_id), INDEX IDX_C74F2195A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, app_token VARCHAR(128) NOT NULL, name VARCHAR(191) NOT NULL, access_token VARCHAR(80) NOT NULL, structure LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, status SMALLINT DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, application_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(191) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(191) DEFAULT NULL, UNIQUE INDEX UNIQ_5933D02C5F37A13B (token), INDEX IDX_5933D02C19EB6921 (client_id), INDEX IDX_5933D02CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, mask VARCHAR(30) NOT NULL, type VARCHAR(20) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, permission_id INT DEFAULT NULL, application_id INT NOT NULL, name VARCHAR(180) NOT NULL, roles LONGTEXT DEFAULT NULL, status SMALLINT DEFAULT 1 NOT NULL, type VARCHAR(191) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6DC044C5FED90CCA (permission_id), INDEX IDX_6DC044C53E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404553E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE user_application ADD CONSTRAINT FK_D401454A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_application ADD CONSTRAINT FK_D4014543E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F219519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id)');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C53E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD6819EB6921');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F219519EB6921');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02C19EB6921');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD68A76ED395');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('ALTER TABLE user_application DROP FOREIGN KEY FK_D401454A76ED395');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195A76ED395');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CA76ED395');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404553E030ACD');
        $this->addSql('ALTER TABLE user_application DROP FOREIGN KEY FK_D4014543E030ACD');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C53E030ACD');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5FED90CCA');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE user_application');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE application_user');
        $this->addSql('DROP TABLE auth_code');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE `group`');
    }
}
