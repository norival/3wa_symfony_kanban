<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531104841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_project (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, role SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_project_user (user_project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_34CE6253B10AD970 (user_project_id), INDEX IDX_34CE6253A76ED395 (user_id), PRIMARY KEY(user_project_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_project_project (user_project_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_AB71B004B10AD970 (user_project_id), INDEX IDX_AB71B004166D1F9C (project_id), PRIMARY KEY(user_project_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_project_user ADD CONSTRAINT FK_34CE6253B10AD970 FOREIGN KEY (user_project_id) REFERENCES user_project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project_user ADD CONSTRAINT FK_34CE6253A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project_project ADD CONSTRAINT FK_AB71B004B10AD970 FOREIGN KEY (user_project_id) REFERENCES user_project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project_project ADD CONSTRAINT FK_AB71B004166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_project_user DROP FOREIGN KEY FK_34CE6253B10AD970');
        $this->addSql('ALTER TABLE user_project_project DROP FOREIGN KEY FK_AB71B004B10AD970');
        $this->addSql('DROP TABLE user_project');
        $this->addSql('DROP TABLE user_project_user');
        $this->addSql('DROP TABLE user_project_project');
    }
}
