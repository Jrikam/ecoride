<form method="POST" action="valider_trajet.php">
  <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
  <label>Tout s'est bien passé ?
    <select name="valider">
      <option value="1">Oui</option>
      <option value="0">Non</option>
    </select>
  </label>
  <br>
  <label>Note (1-5) : <input type="number" name="note" min="1" max="5"></label><br>
  <label>Commentaire : <textarea name="commentaire"></textarea></label>
  <br>
  <button type="submit">Envoyer</button>
</form>
