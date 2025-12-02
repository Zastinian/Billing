import type Egg from "./egg";

export default interface Nest {
  attributes: {
    id: number;
    name: string;
    relationships: {
      eggs: {
        data: Egg[];
      };
    };
  };
}
