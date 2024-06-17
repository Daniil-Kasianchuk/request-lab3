<?php
include("connect.php");

$SELECT_NURSES = "SELECT name FROM nurse";
$SELECT_DEPARTMENTS = "SELECT DISTINCT department FROM nurse";
$SELECT_SHIFTS = "SELECT DISTINCT shift FROM nurse";
try {
    $stmt = $dbh->prepare($SELECT_NURSES);
    $stmt->execute();
    $nurses = $stmt->fetchAll();

    $stmt = $dbh->prepare($SELECT_DEPARTMENTS);
    $stmt->execute();
    $departments = $stmt->fetchAll();

    $stmt = $dbh->prepare($SELECT_SHIFTS);
    $stmt->execute();
    $shifts = $stmt->fetchAll();

} catch (PDOException $ex) {
    echo $ex->GetMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hospital Management</title>
    <link rel="stylesheet" href="index.css">
    <script>
        function sendAjaxRequest(endpoint, data, callback, responseType = 'text') {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', endpoint + '?' + new URLSearchParams(data), true);
            xhr.responseType = responseType;
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    callback(xhr);
                }
            };
            xhr.send();
        }

        function fetchNurseWards() {
            const nurseName = document.getElementById('nurse').value;
            sendAjaxRequest('nurse_wards.php', { nurse: nurseName }, function (xhr) {
                document.getElementById('nurseWardsResults').innerHTML = xhr.responseText;
            });
        }

        function fetchDepartmentNurses() {
            const department = document.getElementById('department').value;
            sendAjaxRequest('department_nurses.php', { department: department }, function (xhr) {
                const nurses = JSON.parse(xhr.responseText);
                let html = '<ul>';
                nurses.forEach(nurse => {
                    html += `<li><b>ID:</b> ${nurse.id_nurse}, <b>Ім'я:</b> ${nurse.name}, <b>Зміна:</b> ${nurse.shift}, <b>Дата:</b> ${nurse.date}</li>`;
                });
                html += '</ul>';
                document.getElementById('departmentNursesResults').innerHTML = html;
            });
        }

        function fetchShiftDuties() {
            const shift = document.getElementById('shift').value;
            sendAjaxRequest('shift_duties.php', { shift: shift }, function (xhr) {
                const duties = xhr.responseXML.getElementsByTagName('duty');
                let html = '<ul>';
                for (let i = 0; i < duties.length; i++) {
                    const name = duties[i].getElementsByTagName('name')[0].textContent;
                    const id = duties[i].getElementsByTagName('id')[0].textContent;
                    const ward = duties[i].getElementsByTagName('ward')[0].textContent;
                    const date = duties[i].getElementsByTagName('date')[0].textContent;
                    html += `<li>${name} (<b>ID:</b> ${id}), <b>Палата:</b> ${ward}, <b>Дата:</b> ${date}</li>`;
                }
                html += '</ul>';
                document.getElementById('shiftDutiesResults').innerHTML = html;
            }, 'document');
        }
    </script>
</head>
<body>
    <h1>Hospital Management</h1>
    
    <h2>Перелік палат, у яких чергує обрана медсестра</h2>
    <form onsubmit="event.preventDefault(); fetchNurseWards();">
        <label for="nurse">Ім'я медсестри:</label>
        <select id="nurse" name="nurse" required>
            <?php
                foreach ($nurses as $row) {
                    echo("<option value='$row[0]'>$row[0]</option>");
                }
            ?>
        </select>
        <input type="submit" value="Отримати перелік палат">
    </form>
    <div id="nurseWardsResults"></div>

    <hr>
    
    <h2>Медсестри обраного відділення</h2>
    <form onsubmit="event.preventDefault(); fetchDepartmentNurses();">
        <label for="department">Відділення:</label>
        <select id="department" name="department" required>
            <?php
                foreach ($departments as $row) {
                    echo("<option value='$row[0]'>$row[0]</option>");
                }
            ?>
        </select>
        <input type="submit" value="Отримати медсестер">
    </form>
    <div id="departmentNursesResults"></div>

    <hr>
    
    <h2>Чергування у зазначену зміну</h2>
    <form onsubmit="event.preventDefault(); fetchShiftDuties();">
        <label for="shift">Зміна:</label>
        <select id="shift" name="shift" required>
            <?php
                foreach ($shifts as $row) {
                    echo("<option value='$row[0]'>$row[0]</option>");
                }
            ?>
        </select>
        <input type="submit" value="Отримати чергування">
    </form>
    <div id="shiftDutiesResults"></div>
</body>
</html>
