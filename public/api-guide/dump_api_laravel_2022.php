<?php

/**
 * @apiDefine token1
 * @apiHeader {String='Bearer <token>'} [Authorization='Bearer 123456'] Authorization Replace <code>token</code> with supplied Auth Token
 * Token sẽ hết hạn sau ... ngày
 */

/**
 * @apiDefine SuccessAndError0
 * @apiSuccess (- Nếu Thành công) {json}  ReturnJson {code: >0 , payload: Data or Message}
 * @apiSuccessExample {json} Ví dụ thành công:
 * {
 * "code": >0,
 * "payload": "Command success",
 * }
 * @apiError  (- Nếu Có lỗi) {json}  ReturnJson {code: <0 , payload: ErrorMessage }
 * @apiErrorExample {json} Ví dụ khi lỗi:
 * {
 * "code": < 0,
 * "payload": "Some error: ...",
 * }
 */

/**
 * @apiDefine Success0
 * @apiSuccess (- Nếu Thành công) {json}  ReturnJson code: >0: ; payload: Message
 * @apiSuccessExample {json} Ví dụ thành công:
 * {
 * "code": >0,
 * "payload": "Command success",
 * }
 */

/**
 * @apiDefine Error0
 * @apiError  (- Nếu Có lỗi) {json}  ReturnJson code: <0: ; payload: Message
 * @apiErrorExample {json} Ví dụ khi lỗi:
 * {
 * "code": < 0,
 * "payload": "Some error: ...",
 * }
 */

class mustHaveThisClass1
{
    
}
?>