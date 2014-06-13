<?php
	include("../layout/pre_content_stuff.php");
?>

<h1>Anmeldung</h1>

<p>
<ul>
<li> Mitmachen kann im Prinzip jeder. Jedoch ist das Ganze als kleineres Projekt geplant und wird diesen Rahmen hoffentlich nicht verlassen.</li>
<li>Bei der Anmeldung muss Vor- und Nachname sowie eine g&uuml;ltige E-Mail-Adresse angeben werden.</li>
</ul>
</p>

<h2>Spielregeln</h2>
<ul>
<li> Getippt werden die <b> Spielergebnisse nach regul&auml;rer Spielzeit</b> (90 Min. + Nachspielzeit), d.h. in der Endrunde wird eine m&ouml;gliche <b>Verl&auml;ngerung oder Elfmeterschie&szlig;en nicht ber&uuml;cksichtigt.</b></li>
<li> Abgabe und &Auml;nderung des Spieltipps ist bis zum offiziellen Spielbeginn m&ouml;glich.
<li> Außerdem kann bis zum Anpfiff des Er&ouml;ffnungsspiels ein Tipp für den Sieger des Turniers (Meistertipp) abgegeben werden. </li>
</ul>

  <h2>Punktewertung</h2>
  <ul>  <li> Gewinner ist, wer am Ende des Turniers die meisten Punkte hat. Die Punkte werden folgenderma&szlig;en vergeben: 
<p>
     <table id="Highscore">
         <tr>
           <th></th>
           <th style="text-align:center" width="100">Ergebnis richtig</th>
           <th style="text-align:center" width="100">Tordifferenz richtig</th>
           <th style="text-align:center" width="100">Sieger/Unentschieden richtig</th>
         </tr>
         <tr>

<?php
echo "  <th align='left'>Tipp auf Sieg Mannschaft A</th>";
echo "           <td style='text-align:center'>", SCORE_RESULT, "</td>";
echo "           <td style='text-align:center'>", SCORE_DIFF, "</td>";
echo "           <td style='text-align:center'>", SCORE_TENDENCY, "</td>";
echo "         </tr>";
echo "         <tr>";
echo "           <th align='left'>Tipp auf Unentschieden</th>";
echo "           <td style='text-align:center'>", SCORE_DRAW_RESULT, "</td>";
echo "           <td colspan='2' style='text-align:center'>", SCORE_DRAW_TENDENCY, "</td>";
echo "         </tr>";
echo "         <tr>";
echo "           <th align='left'>Tipp auf Sieg Mannschaft B</th>";
echo "           <td style='text-align:center'>", SCORE_RESULT, "</td>";
echo "           <td style='text-align:center'>", SCORE_DIFF, "</td>";
echo "           <td style='text-align:center'>", SCORE_TENDENCY, "</td>";
echo "         </tr>";
echo "     </table>";
echo "</p>";
echo "</li>";
echo "";
echo "<li>Ein Beispiel:";
echo "<ul>";
echo "<li>     Spieler A tippt bei drei Spielen 3:2, 1:1 und 3:0, Spieler B 0:0, 2:2 und 2:3.</li>";
echo "<li>     Ergebnis der Spiele nach regul&auml;rer Spielzeit: 1:0, 2:2 und 2:3.</li>";
$example = SCORE_DIFF + SCORE_TENDENCY;
echo "<li>     Spieler A bekommt ", SCORE_DIFF, " + ", SCORE_TENDENCY, " + 0 = ", $example, " Punkte. </li>";
$example = SCORE_RESULT + SCORE_RESULT;
echo "<li>     Spieler B bekommt 0 + ", SCORE_RESULT, " + ", SCORE_RESULT, " = ", $example, " Punkte. </li>";
echo "</ul>";
echo "    </li>";
echo "<li> Ein richtiger Meistertipp ist zus&auml;tzlich ", SCORE_CHAMPTIP ," Punkte wert. </li>";
echo "<li> Haben am Ende mehrere Spieler dieselbe Punktzahl, wird der Sieger nach folgenden Kriterien ermittelt:";
echo " <ol>";
echo " <li> Anzahl der Punkte ohne Meistertipp </li>";
echo " <li> Anzahl der richtig getippten Ergebnisse </li>";
echo " <li> Anzahl der richtig getippten Tordifferenzen </li>";
echo " <li> Anzahl der richtig getippten Sieger </li>";
echo " <li> Anzahl der gewonnen Punkte im letzten Spiel. Bei Gleichheit wird das Kriterium iterativ auf die vorangegangenen Spiele angewendet. </li>";
echo " </ol>";
echo "</li>";
echo "</ul>";
?>

<h2>Preise</h2>
... gibt es nat&uuml;rlich auch: Ruhm, Ehre - und einen Meisterstern für den Sieger. Dieser wird bei allen weiteren Tippspielen direkt beim Namen angezeigt!

<h2>Wettbewerb</h2>
Am <b>Wettbewerb</b> nehmen alle Tipper teil, die auf dem Party-Verteiler stehen. Der Sieger erh&auml;lt den offiziellen Wanderpokal, der zur WM 2006 eingef&uuml;hrt wurde.



<?php
	include("../layout/post_content_stuff.php");
?>
