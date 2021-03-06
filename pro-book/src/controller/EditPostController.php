<?php
class EditPostController implements ControllerInterface
{
    private static function isName($value)
    {
        return preg_match("/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/", $value);
    }

    private static function isNum($value)
    {
        return preg_match('/^\d+$/', $value);
    }

    private static function isCardNumber(string $cardNumber)
    {
        $api = Api::validateCardNumber($cardNumber);
        return $api->valid;
    }

    public static function control(Request $request)
    {
        $db = new MarufDB();
        $user = $db->getUser($_COOKIE['token']);
        $userId = $user['id'];
        $name = $user['name'];
        $address = $user['address'];
        $phoneNumber = $user['phonenumber'];
        $cardNumber = $user['cardnumber'];
        $imageUrl = $user['imageurl'];

        $response = array('error' => array());
        $response['success'] = true;
        $uploadOk = 1;

        if ($_FILES['fileToUpload']['size'] > 0) {
            $targetDir = "src/model/profile/";
            $targetFile = $targetDir . $userId . '.jpg';
            $imageFileType = strtolower(pathinfo($targetDir . basename($_FILES["fileToUpload"]["name"]), PATHINFO_EXTENSION));

            if (isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if ($check !== false) {
                    echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    $response['success'] = false;
                    $response['error'][] = 'File must be an image';
                    $uploadOk = 0;
                }
            }

            if ($imageFileType != "jpg") {
                $response['success'] = false;
                $response['error'][] = 'Image file must be in JPG format';
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                $response['success'] = false;
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
                    $response['success'] = true;
                } else {
                    $response['success'] = false;
                }
            }
        }

        $result = 0;
        if ($response['success'] && $uploadOk) {
            if (!(EditPostController::isName($request->name) && strlen($request->name) > 0 && strlen($request->name) <= 20)) {
                $response['success'] = false;
                $response['error'][] = 'Name must be a valid person name with less than 20 characters';
            } else if (strlen($request->address) == 0) {
                $response['success'] = false;
                $response['error'][] = 'Address cannot be empty';
            } else if (!(EditPostController::isNum($request->phoneNumber) && strlen($request->phoneNumber) >= 9 && strlen($request->phoneNumber) <= 12)) {
                $response['success'] = false;
                $response['error'][] = 'Phone number must be a number with 9 to 12 digits';
            } else if (!(EditPostController::isCardNumber($request->cardNumber))) {
                $response['success'] = false;
                $response['error'][] = 'Card number invalid';
            } else {
                $name = $request->name;
                $address = $request->address;
                $phoneNumber = $request->phoneNumber;
                $cardNumber = $request->cardNumber;
                $response['success'] = true;
                $result = $db->editProfile($request->name, $request->address, $request->phoneNumber, $request->userId, $request->cardNumber, $request->imageUrl);
            }
        }

        if (!$result) {
            $response['success'] = false;
            $response['error'][] = 'Internal error';
        }

        $template = new Template('src/view/edit.php');
        if ($imageUrl) {
            return $template->render($user['id'], $name, $user['username'], $user['email'], $address, $phoneNumber, $cardNumber, $imageUrl, $response);
        }
        return $template->render($user['id'], $name, $user['username'], $user['email'], $address, $phoneNumber, $cardNumber, '', $response);
    }
}
