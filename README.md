# EcoRide

**EcoRide** est une application de covoiturage éco-responsable permettant à des chauffeurs et passagers de s’organiser, de réserver et de gérer des trajets.
Maquettes
📱 Version mobile :

    Page d’accueil : https://www.figma.com/design/Eeua7OAKxeGD1YlkRdvlQ8/Untitled?node-id=0-1&m=dev&t=JlzbxxKpfkUFc1py-1

    Page historique

    Page détails covoiturages

💻 Version desktop :

    Page d’accueil : https://drive.google.com/file/d/1ztCzIhTRpKP38gXuURwLXDOHcPGcQ2X1/view?usp=sharing

    Page historique : https://drive.google.com/file/d/1PHjemldeQoFh42ULsShqjOrC9pIn0O0q/view?usp=sharing

    Page détail trajets : https://drive.google.com/file/d/1rDRb2g8Co121oRgt5Y4tibZ8P_iSkvQB/view?usp=sharing

📋 Charte graphique

    https://www.canva.com/design/DAGt1ILCP50/RBdNBx8hBh-WqDByFohdYA/edit?utm_content=DAGt1ILCP50&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton

📌 Kanban (Trello)

    https://trello.com/b/LBCphV8r/mod%C3%A8le-kanban

📄 Documentation projet

    https://drive.google.com/file/d/1wg97mjUc0lovD7buKNSH-yvzCgb0T3kS/view?usp=sharing

Composition 
https://drive.google.com/file/d/1NEvBhtvvxwt2s6vxVv9D7BmaDaM4QXCG/view?usp=sharing

    

## 🚀 Lancer l’application en local

### Prérequis :
- Docker & Docker Compose installés

### Installation :

```bash
git clone https://github.com/Jrikam/ecoride.git
cd ecoride
docker-compose up --build
## 🔗 Accès à l'application
- URL locale : http://localhost:8080/

## 👤 Connexions possibles

| Rôle          | Email                     | Mot de passe   |
|---------------|---------------------------|----------------|
| Utilisateur   | jaaderikam@gmail.com  |file |
| Employé       | evamiolard@gmail.com | Password |
|Administrateur  | monadmin@test.com  | admin123 | c'est le mdp entier avec le tiret à coté  | 

## 🛠 Structure Git

- `main` : branche stable (prod)
- `develop` : branche de développement
- `usX-*` : une branche par user story
  - Ex : `us6-login`, `us7-creer-compte`, `us13-admin`, etc.
