<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250312211219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3D5E258C5');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B367B3B43D');
        $this->addSql('DROP INDEX IDX_AC6340B3D5E258C5 ON `like`');
        $this->addSql('DROP INDEX IDX_AC6340B367B3B43D ON `like`');
        $this->addSql('ALTER TABLE `like` ADD user_id INT NOT NULL, ADD post_id INT NOT NULL, DROP users_id, DROP posts_id');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_AC6340B3A76ED395 ON `like` (user_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B34B89032C ON `like` (post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B34B89032C');
        $this->addSql('DROP INDEX IDX_AC6340B3A76ED395 ON `like`');
        $this->addSql('DROP INDEX IDX_AC6340B34B89032C ON `like`');
        $this->addSql('ALTER TABLE `like` ADD users_id INT NOT NULL, ADD posts_id INT NOT NULL, DROP user_id, DROP post_id');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3D5E258C5 FOREIGN KEY (posts_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B367B3B43D FOREIGN KEY (users_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_AC6340B3D5E258C5 ON `like` (posts_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B367B3B43D ON `like` (users_id)');
    }
}
