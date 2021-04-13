# Endpoints

Здесь хранятся EndPoints (могут называться по-разному - Controllers, Actions, Commands).

Их основная задача получить данные из ввода, проверять их и передать их в UseCase.

Обычно этот слой обильно использует функционал фреймворка, т.к. получает от этого
максимальное преимущество, а абстракция от фреймворка здесь почти ничего не дает, т.к. слой максимально тонкий.

Typical controller looks like
```php
class Controller
    public function __invoke(Request $request): FoundResponse
    {
        $arg1 = $request->getParam1();
        $arg2 = $request->getParam2();
        try {
            $result = $this->usecase->execute($arg1, $arg2);
        } catch (NothingFound $e) {
            throw new NotFoundHttpException('nothing', $e);
        } catch (AlreadyProceed $e) {
            throw new ConflictHttpException('conflict', $e);
        } catch (BadStatus $e) {
            throw new UnprocessableEntity('conflict', $e);
        }
        
        return new FoundResponse($result);
    }
}
```