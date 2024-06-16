<?php

class CatController
{
    public function __construct(private CatGateway $gateway)
    {
    }

    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $cat = $this->gateway->get($id);
        if ($cat === false) {
            http_response_code(404);
            echo json_encode(['message' => 'Cat not found']);
            exit;
        }

        switch ($method) {
            case 'GET':
                http_response_code(200);
                echo json_encode($cat);
                break;

            case 'PATCH':
                $data = (array)json_decode(file_get_contents('php://input'), true);

                $errors = $this->getValidationErrors($data, false);
                if (count($errors)) {
                    http_response_code(422);
                    echo json_encode(['errors' => $errors]);
                    exit;
                }

                $rowCount = $this->gateway->update($cat, $data);

                echo json_encode([
                    'message' => "Cat $id updated",
                    'rows' => $rowCount
                ]);
                break;

            case 'DELETE':
                http_response_code(200);
                $rowCount = $this->gateway->delete($id);
                echo json_encode([
                    'message' => "Cat $id deleted",
                    'rows' => $rowCount
                ]);
                break;

            default:
                http_response_code(405);
                header('Allow: GET, PATCH, DELETE');
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case 'GET':
                http_response_code(200);
                echo json_encode($this->gateway->getAll());
                break;

            case 'POST':
                $data = (array)json_decode(file_get_contents('php://input'), true);

                $errors = $this->getValidationErrors($data);
                if (count($errors)) {
                    http_response_code(422);
                    echo json_encode(['errors' => $errors]);
                    exit;
                }

                $id = $this->gateway->create($data);

                http_response_code(201);
                echo json_encode([
                    'message' => 'Cat created',
                    'id' => $id
                ]);
                break;

            default:
                http_response_code(405);
                header('Allow: GET, POST');
        }
    }

    private function getValidationErrors(array $data, bool $isNew = true): array
    {
        $errors = [];

        if ($isNew && empty($data['name'])) {
            $errors[] = 'name is required';
        }

        if (array_key_exists('age', $data)) {
            if (filter_var($data['age'], FILTER_VALIDATE_INT) === false) {
                $errors[] = 'age must be an integer';
            }
        }

        if (array_key_exists('is_happy', $data)) {
            if (filter_var($data['is_happy'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === null) {
                $errors[] = 'is_happy must be an boolean';
            }
        }

        return $errors;
    }
}
