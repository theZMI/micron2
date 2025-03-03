export function isServerVersionNewer(currentModel, apiData) {
    if (!currentModel.isExists()) { // Если в локальной версии такой модели нет, то это новая задача
        return true;
    }
    if (+apiData.last_update_time > +currentModel.last_update_time) { // Если время последнего обновления задачи на сервере больше, значит применим обновление
        return true;
    }
    return false; // Иначе оставляем те данные в задаче которые были (так как мы могли добавить описание, картинку и пр., а они ещё не синхронизировались)
}