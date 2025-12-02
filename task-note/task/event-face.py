# face_embedder.py
import sys
import numpy as np
from PIL import Image
import tensorflow as tf

def preprocess_image(image_path):
    img = Image.open(image_path).convert('RGB')
    img = img.resize((112, 112))
    img_array = np.array(img).astype(np.float32) / 255.0
    img_array = np.expand_dims(img_array, axis=0)  # [1, 112, 112, 3]
    return img_array

def get_embedding(image_path):
    interpreter = tf.lite.Interpreter(model_path="/var/www/html/task-note/task/mobilefacenet_mc.tflite")
    interpreter.allocate_tensors()

    input_details = interpreter.get_input_details()
    output_details = interpreter.get_output_details()

    input_data = preprocess_image(image_path)
    interpreter.set_tensor(input_details[0]['index'], input_data)
    interpreter.invoke()

    output_data = interpreter.get_tensor(output_details[0]['index'])  # shape [1, 192] or [1, 128]
    embedding = output_data[0]
    return embedding

if __name__ == "__main__":
    image_path = sys.argv[1]
    emb = get_embedding(image_path)
    print(",".join([str(x) for x in emb]))
