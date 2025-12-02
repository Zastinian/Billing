interface Entity {
  id: number;
  values: number[];
}

export const isValidFormat = (input: string): boolean => {
  const [id, value] = input.split(":");
  return /^\d+:\d+$/.test(input) && !Number.isNaN(Number(id)) && !Number.isNaN(Number(value));
};

export default (input: string): Entity[] => {
  const entityMap: Record<number, number[]> = {};
  const pairs = input.split(",");
  pairs.forEach((pair) => {
    const [entityId, value] = pair.split(":").map(Number);

    if (!entityMap[entityId]) {
      entityMap[entityId] = [];
    }
    entityMap[entityId].push(value);
  });
  const entities: Entity[] = Object.keys(entityMap).map((id) => ({
    id: Number(id),
    values: entityMap[Number(id)],
  }));
  return entities;
};
