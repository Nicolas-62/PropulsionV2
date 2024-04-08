<?php

namespace App\Entity;

use App\Entity\Traits\ErrorTrait;
use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config
{
    use ErrorTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_contact = null;

    #[ORM\Column(length: 255)]
    private ?string $email_objet = null;

    #[ORM\Column]
    private ?int $cache_flush_auto = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoDescription = null;

    #[ORM\Column]
    private ?array $seoKeywords = array();

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailBilletterie = null;

    #[ORM\Column(length: 255)]
    private ?string $emailComptabilite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailPartenariats = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailCommunication = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailProjets = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailProgrammation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailTechnique = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailBilletterieObject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailComptabiliteObject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailPartenariatsObject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailCommunicationObject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailProjetsObject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailProgrammationObject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailTechniqueObject = null;


    public function getSubjectEmailById($subject_id){
        $subject = Contact::SUBJECTS[$subject_id];
        return $this->{'getEmail'.ucfirst($subject['variable'])}();
    }

    public function getSubjectObjectById($subject_id){
        $subject = Contact::SUBJECTS[$subject_id];
        return $this->{'getEmail'.ucfirst($subject['variable']).'Object'}();
    }

    /**
     * Retournes les données SEO du site
     * @return Seo
     */
    public function getSeo(): Seo
    {
        $seo = new Seo();
        $seo->setTitle($this->getSeoTitle());
        $seo->setDescription($this->getSeoDescription());
        $seo->setKeywords($this->getSeoKeywords());
        return $seo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailContact(): ?string
    {
        return $this->email_contact;
    }

    public function setEmailContact(?string $email_contact): self
    {
        $this->email_contact = $email_contact;

        return $this;
    }

    public function getEmailObjet(): ?string
    {
        return $this->email_objet;
    }

    public function setEmailObjet(string $email_objet): self
    {
        $this->email_objet = $email_objet;

        return $this;
    }

    public function getCacheFlushAuto(): ?int
    {
        return $this->cache_flush_auto;
    }

    public function setCacheFlushAuto(int $cache_flush_auto): self
    {
        $this->cache_flush_auto = $cache_flush_auto;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Methode nessecaire pour appel des elements dans un sélecteur de formulaire.
     *
     * @return string
     */
      public function __toString(): string
      {
        return 'Configuration du site';
      }

  public function getSeoTitle(): ?string
  {
      return $this->seoTitle;
  }

  public function setSeoTitle(?string $seoTitle): self
  {
      $this->seoTitle = $seoTitle;

      return $this;
  }

  public function getSeoDescription(): ?string
  {
      return $this->seoDescription;
  }

  public function setSeoDescription(?string $seoDescription): self
  {
      $this->seoDescription = $seoDescription;

      return $this;
  }

    /**
     * @return array|null
     */
    public function getSeoKeywords(): ?array
    {
        return $this->seoKeywords;
    }

    /**
     * @param array|null $seoKeywords
     */
    public function setSeoKeywords(?array $seoKeywords): void
    {
        $this->seoKeywords = $seoKeywords;
    }

    public function getEmailBilletterie(): ?string
    {
        return $this->emailBilletterie;
    }

    public function setEmailBilletterie(?string $emailBilletterie): self
    {
        $this->emailBilletterie = $emailBilletterie;

        return $this;
    }

    public function getEmailComptabilite(): ?string
    {
        return $this->emailComptabilite;
    }

    public function setEmailComptabilite(string $emailComptabilite): self
    {
        $this->emailComptabilite = $emailComptabilite;

        return $this;
    }

    public function getEmailPartenariats(): ?string
    {
        return $this->emailPartenariats;
    }

    public function setEmailPartenariats(?string $emailPartenariats): self
    {
        $this->emailPartenariats = $emailPartenariats;

        return $this;
    }

    public function getEmailCommunication(): ?string
    {
        return $this->emailCommunication;
    }

    public function setEmailCommunication(?string $emailCommunication): self
    {
        $this->emailCommunication = $emailCommunication;

        return $this;
    }

    public function getEmailProjets(): ?string
    {
        return $this->emailProjets;
    }

    public function setEmailProjets(?string $emailProjets): self
    {
        $this->emailProjets = $emailProjets;

        return $this;
    }

    public function getEmailProgrammation(): ?string
    {
        return $this->emailProgrammation;
    }

    public function setEmailProgrammation(?string $emailProgrammation): self
    {
        $this->emailProgrammation = $emailProgrammation;

        return $this;
    }

    public function getEmailTechnique(): ?string
    {
        return $this->emailTechnique;
    }

    public function setEmailTechnique(?string $emailTechnique): self
    {
        $this->emailTechnique = $emailTechnique;

        return $this;
    }

    public function getEmailBilletterieObject(): ?string
    {
        return $this->emailBilletterieObject;
    }

    public function setEmailBilletterieObject(?string $emailBilletterieObject): self
    {
        $this->emailBilletterieObject = $emailBilletterieObject;

        return $this;
    }

    public function getEmailComptabiliteObject(): ?string
    {
        return $this->emailComptabiliteObject;
    }

    public function setEmailComptabiliteObject(?string $emailComptabiliteObject): self
    {
        $this->emailComptabiliteObject = $emailComptabiliteObject;

        return $this;
    }

    public function getEmailPartenariatsObject(): ?string
    {
        return $this->emailPartenariatsObject;
    }

    public function setEmailPartenariatsObject(?string $emailPartenariatsObject): self
    {
        $this->emailPartenariatsObject = $emailPartenariatsObject;

        return $this;
    }

    public function getEmailCommunicationObject(): ?string
    {
        return $this->emailCommunicationObject;
    }

    public function setEmailCommunicationObject(?string $emailCommunicationObject): self
    {
        $this->emailCommunicationObject = $emailCommunicationObject;

        return $this;
    }

    public function getEmailProjetsObject(): ?string
    {
        return $this->emailProjetsObject;
    }

    public function setEmailProjetsObject(?string $emailProjetsObject): self
    {
        $this->emailProjetsObject = $emailProjetsObject;

        return $this;
    }

    public function getEmailProgrammationObject(): ?string
    {
        return $this->emailProgrammationObject;
    }

    public function setEmailProgrammationObject(?string $emailProgrammationObject): self
    {
        $this->emailProgrammationObject = $emailProgrammationObject;

        return $this;
    }

    public function getEmailTechniqueObject(): ?string
    {
        return $this->emailTechniqueObject;
    }

    public function setEmailTechniqueObject(?string $emailTechniqueObject): self
    {
        $this->emailTechniqueObject = $emailTechniqueObject;

        return $this;
    }

}
